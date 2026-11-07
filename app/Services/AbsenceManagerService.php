<?php

namespace App\Services;

use App\Models\Turno;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Mail\TurnoReasignadoMail;
use App\Http\Mail\TurnoContingenciaMail;

class AbsenceManagerService
{
    /**
     * Procesa la inasistencia aplicando los 4 niveles de rescate.
     */
    public function procesarInasistenciaBarbero(array $turnosAsignados)
    {
        foreach ($turnosAsignados as $turnoId => $sustitutoIdElegido) {
            $turno = Turno::with(['servicio', 'servicios', 'cliente'])->find($turnoId);
            if (!$turno) {
                continue;
            }

            $fecha = $turno->fecha;
            $hora = $turno->hora;
            $sucursalId = $turno->sucursal_id;

            // Calcular la duración total del turno
            $duracion = $turno->servicios->isNotEmpty() 
                ? $turno->servicios->sum('duracion') 
                : ($turno->servicio->duracion ?? 30);

            // Obtener personal válido de la sucursal
            $personalValido = Usuario::where('sucursal_id', $sucursalId)
                ->where('id', '!=', $turno->barbero_id) 
                ->whereIn('rol_id', [1, 2]) 
                ->where('activo', 1)
                ->whereHas('turnoTrabajo', function($query) {
                    $query->whereNotNull('hora_inicio');
                })
                ->get();

            $barberoFinalId = null;
            $barberoFinalObj = null;

            // --- NIVEL 1: Cascada Directa (Manual si se eligió, o automática en horario exacto) ---
            $resultadoNivel1 = $this->intentarNivel1($turno, $personalValido, $sustitutoIdElegido, $sucursalId, $fecha, $hora, $duracion);
            if ($resultadoNivel1) {
                $barberoFinalId = $resultadoNivel1['id'];
                $barberoFinalObj = $resultadoNivel1['obj'];
            }

            // --- NIVEL 2: Huecos Flexibles (Búsqueda de espacio más tarde en el día) ---
            if (!$barberoFinalId) {
                $resultadoNivel2 = $this->intentarNivel2($turno, $personalValido, $sucursalId, $fecha, $hora, $duracion);
                if ($resultadoNivel2) {
                    $barberoFinalId = $resultadoNivel2['id'];
                    $barberoFinalObj = $resultadoNivel2['obj'];
                }
            }

            // --- NIVEL 3: El Administrador Comodín (Extensión de jornada) ---
            if (!$barberoFinalId) {
                $resultadoNivel3 = $this->intentarNivel3AdminComodin($turno, $sucursalId, $fecha, $hora, $duracion);
                if ($resultadoNivel3) {
                    $barberoFinalId = $resultadoNivel3['id'];
                    $barberoFinalObj = $resultadoNivel3['obj'];
                }
            }

            // --- APLICAR REASIGNACIÓN O NIVEL 4 (CONTINGENCIA) ---
            if ($barberoFinalId) {
                $turno->update([
                    'barbero_id' => $barberoFinalId
                ]);

                // Enviar correo de éxito al cliente
                if (!empty($turno->cliente?->email)) {
                    try {
                        Mail::to($turno->cliente->email)->send(new TurnoReasignadoMail($turno, $barberoFinalObj));
                    } catch (\Exception $e) {
                        \Log::error("Error al enviar correo de reasignación (Turno ID: {$turno->id}): " . $e->getMessage());
                    }
                }
            } else {
                // --- NIVEL 4: Contingencia Real ("Bomba") ---
                $turno->update([
                    'estado' => 'sin_asignar_contingencia'
                ]);
                
                \Log::warning("Contingencia de inasistencia: El turno ID {$turno->id} (Sucursal: {$sucursalId}, Fecha: {$fecha} {$hora}) no encontró reemplazo en ningún nivel.");

                // Notificar al cliente de la contingencia
                if (!empty($turno->cliente?->email)) {
                    try {
                        Mail::to($turno->cliente->email)->send(new TurnoContingenciaMail($turno));
                    } catch (\Exception $e) {
                        \Log::error("Error al enviar correo de contingencia al cliente (Turno ID: {$turno->id}): " . $e->getMessage());
                    }
                }
            }
        }
    }

    private function intentarNivel1(Turno $turno, $personalValido, $sustitutoIdElegido, $sucursalId, $fecha, $hora, $duracion)
    {
        // Si el admin eligió un sustituto manual válido
        if (!empty($sustitutoIdElegido)) {
            $sustitutoValido = $personalValido->firstWhere('id', $sustitutoIdElegido);
            if ($sustitutoValido && !$this->existeConflictoHorario($sustitutoIdElegido, $sucursalId, $fecha, $hora, $duracion, $turno->id)) {
                return ['id' => $sustitutoIdElegido, 'obj' => $sustitutoValido];
            }
        }

        // Cascada automática en horario exacto (si no se eligió manual o el manual tenía choque)
        foreach ($personalValido as $alternativo) {
            if (!empty($sustitutoIdElegido) && $alternativo->id == $sustitutoIdElegido) {
                continue; 
            }

            if (!$this->existeConflictoHorario($alternativo->id, $sucursalId, $fecha, $hora, $duracion, $turno->id)) {
                return ['id' => $alternativo->id, 'obj' => $alternativo];
            }
        }

        return null;
    }

    private function intentarNivel2(Turno $turno, $personalValido, $sucursalId, $fecha, $horaOriginal, $duracion)
    {
        $parsedHora = Carbon::parse($horaOriginal);

        // Buscar en incrementos de 30 minutos hacia adelante (hasta 3 horas después)
        for ($i = 1; $i <= 6; $i++) {
            $nuevaHora = $parsedHora->copy()->addMinutes($i * 30)->format('H:i:s');

            foreach ($personalValido as $colega) {
                if (!$this->existeConflictoHorario($colega->id, $sucursalId, $fecha, $nuevaHora, $duracion, $turno->id)) {
                    $turno->update(['hora' => $nuevaHora]); // Actualizamos la hora del turno al nuevo hueco
                    return ['id' => $colega->id, 'obj' => $colega];
                }
            }
        }

        return null;
    }

    private function intentarNivel3AdminComodin(Turno $turno, $sucursalId, $fecha, $hora, $duracion)
    {
        // Buscar administrador principal de la sucursal (rol_id = 1)
        $adminComodin = Usuario::where('sucursal_id', $sucursalId)
            ->where('rol_id', 1)
            ->where('activo', 1)
            ->first();

        if ($adminComodin) {
            return ['id' => $adminComodin->id, 'obj' => $adminComodin];
        }

        return null;
    }

    private function existeConflictoHorario($barberoId, $sucursalId, $fecha, $horaInicio, $duracionMinutos, $turnoIdExcluir): bool
    {
        $inicioNuevo = Carbon::parse($fecha . ' ' . $horaInicio);
        $finNuevo = $inicioNuevo->copy()->addMinutes($duracionMinutos);

        $turnosExistentes = Turno::where('barbero_id', $barberoId)
            ->where('fecha', $fecha)
            ->where('id', '!=', $turnoIdExcluir)
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->get();

        foreach ($turnosExistentes as $t) {
            $duracionExistente = $t->servicios->isNotEmpty() ? $t->servicios->sum('duracion') : ($t->servicio->duracion ?? 30);
            $inicioExistente = Carbon::parse($t->fecha . ' ' . $t->hora);
            $finExistente = $inicioExistente->copy()->addMinutes($duracionExistente);

            if ($inicioNuevo < $finExistente && $finNuevo > $inicioExistente) {
                return true; // Hay solapamiento
            }
        }

        return false;
    }
}