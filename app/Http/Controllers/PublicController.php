<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoServicio;
use App\Models\Sucursal;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Models\Usuario; // Modelo correcto del proyecto

class PublicController extends Controller
{
    /**
     * Página principal del usuario/visitante (Landing Page pública).
     * Carga y consolida de forma optimizada toda la información institucional y comercial:
     * tipos de servicios activos, sucursales, profesionales disponibles, servicios populares por demanda,
     * novedades y testimonios reales calificados con 4 o 5 estrellas.
     */
    public function index()
    {
        // 1. Tipos de servicio con sus respectivos servicios activos vinculados
        $tipos = TipoServicio::with(['servicios' => function($q){
            $q->where('activo', true);
        }])->where('activo', true)->get();

        // 2. Sucursales comerciales activas
        $sucursales = Sucursal::where('activo', true)->get();

        // 3. Profesionales activos con sucursal asignada y turno de trabajo definido (barberos y administradores operativos)
        $barberos = Usuario::with('sucursal')
            ->whereNotNull('sucursal_id')
            ->whereNotNull('turno_trabajo_id')
            ->get(); 

        // 4. Imagen de fondo principal para el banner (Hero section)
        $heroImagen = asset('images/hero_barberia.jpg');

        // 5. Servicios más populares ordenados dinámicamente según la cantidad de turnos históricos (nivel de demanda)
        $populares = Servicio::withCount('turnos')
            ->where('activo', true)
            ->orderBy('turnos_count', 'desc')
            ->take(6)
            ->get();

        // 6. Últimos servicios agregados al catálogo recientemente
        $ultimos = Servicio::where('activo', true)
            ->latest()
            ->take(10)
            ->get();

        // 7. Testimonios: Calificaciones reales y activas de clientes con puntuaciones de 4 y 5 estrellas
        $testimonios = Calificacion::with(['cliente', 'turno.servicio', 'turno.barbero'])
            ->whereIn('puntuacion', [4, 5])
            ->where('activo', true) // Filtro de moderación para asegurar que solo se muestren comentarios aprobados
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('usuario.index', compact('tipos', 'sucursales', 'barberos', 'heroImagen', 'populares', 'ultimos', 'testimonios'));
    }

    /**
     * Muestra todos los servicios asociados a un tipo de servicio específico.
     */
    public function showTipo($id)
    {
        $tipo = TipoServicio::with('servicios')->findOrFail($id);
        return view('usuario.tiposervicios.show', compact('tipo'));
    }

    /**
     * Muestra el detalle e información ampliada de un servicio puntual seleccionado.
     */
    public function showServicio($id)
    {
        $servicio = Servicio::findOrFail($id);
        return view('usuario.servicios.show', compact('servicio'));
    }
}