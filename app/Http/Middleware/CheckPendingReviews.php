<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Turno;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class CheckPendingReviews
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Verificar si hay un usuario logueado
        if (Auth::check() && Auth::user()->rol && Auth::user()->rol->nombre === 'cliente') {
            
            // 2. Buscar si tiene algún turno 'completado' que AÚN NO tenga calificación
            $turnoPendienteCalificar = Turno::where('cliente_id', Auth::id())
                ->where('estado', 'completado')
                ->whereDoesntHave('calificacion') // Si ya calificó, no lo trae
                ->latest('fecha')
                ->first();

            // 3. Compartir esa información con las vistas
            View::share('turnoPendienteCalificar', $turnoPendienteCalificar);
        }

        return $next($request);
    }
}