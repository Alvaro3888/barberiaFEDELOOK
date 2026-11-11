<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Calificacion;

class CalificacionController extends Controller
{
    /**
     * Almacena una nueva calificación y comentario realizada por un cliente sobre un turno completado.
     * Valida la existencia del turno, una puntuación válida de 1 a 5 estrellas, 
     * y aplica restricciones de formato al comentario (mínimo de caracteres y que obligatoriamente empiece con letra).
     */
    public function store(Request $request)
    {
        // 1. Validaciones estrictas de datos de entrada y mensajes de error personalizados
        $request->validate([
            'turno_id'   => 'required|exists:turnos,id',
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => [
                'required',
                'string',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ].*/' // Expresión regular para obligar a que el texto comience con una letra y evitar spam de símbolos
            ],
        ], [
            'comentario.required' => 'El comentario es obligatorio para enviar la calificación.',
            'comentario.regex'    => 'El comentario debe comenzar obligatoriamente con una letra.',
            'comentario.min'      => 'El comentario debe tener al menos 3 caracteres.',
        ]);

        // 2. Persistencia de la calificación vinculada al turno y al cliente autenticado en sesión
        Calificacion::create([
            'turno_id'   => $request->turno_id,
            'cliente_id' => auth()->id(),
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', '¡Muchas gracias por dejarnos tu experiencia!');
    }
}