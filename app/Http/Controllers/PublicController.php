<?php

namespace App\Http\Controllers;

use App\Models\TipoServicio;
use App\Models\Sucursal;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Models\Usuario; // Modelo correcto del proyecto

class PublicController extends Controller
{
    // Página principal del usuario/visitante
    public function index()
    {
        // Tipos de servicio con sus servicios activos
        $tipos = TipoServicio::with(['servicios' => function($q){
            $q->where('activo', true);
        }])->where('activo', true)->get();

        // Sucursales activas
        $sucursales = Sucursal::where('activo', true)->get();

        // Profesionales activos con sucursal y turno de trabajo definido (incluye barberos y admins con turno)
        $barberos = Usuario::with('sucursal')
            ->whereNotNull('sucursal_id')
            ->whereNotNull('turno_trabajo_id')
            ->get(); 

        // Imagen de fondo para el hero
        $heroImagen = asset('images/hero_barberia.jpg');

        // Servicios populares ordenados dinámicamente por la cantidad de turnos (demanda)
        $populares = Servicio::withCount('turnos')
            ->where('activo', true)
            ->orderBy('turnos_count', 'desc')
            ->take(6)
            ->get();

        // Últimos agregados
        $ultimos = Servicio::where('activo', true)
            ->latest()
            ->take(10)
            ->get();

        // Traer las calificaciones reales activas desde la base de datos
        $testimonios = Calificacion::with(['cliente', 'turno.servicio', 'turno.barbero'])
            ->whereIn('puntuacion', [4, 5])
            ->where('activo', true) // <--- ESTO ES LO QUE FALTABA
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('usuario.index', compact('tipos', 'sucursales', 'barberos', 'heroImagen', 'populares', 'ultimos', 'testimonios'));
    }

    // Mostrar todos los modelos de un tipo de servicio
    public function showTipo($id)
    {
        $tipo = TipoServicio::with('servicios')->findOrFail($id);
        return view('usuario.tiposervicios.show', compact('tipo'));
    }

    // Mostrar detalle de un servicio puntual
    public function showServicio($id)
    {
        $servicio = Servicio::findOrFail($id);
        return view('usuario.servicios.show', compact('servicio'));
    }
}