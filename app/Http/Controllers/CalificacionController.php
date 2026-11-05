<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Calificacion;

class CalificacionController extends Controller
{
  public function store(Request $request)
    {
        $request->validate([
            'turno_id'   => 'required|exists:turnos,id',
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => [
                'required',
                'string',
                'min:3',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ].*/'
            ],
        ], [
            'comentario.required' => 'El comentario es obligatorio para enviar la calificación.',
            'comentario.regex'    => 'El comentario debe comenzar obligatoriamente con una letra.',
            'comentario.min'      => 'El comentario debe tener al menos 3 caracteres.',
        ]);

        // Guardar la calificación usando $request->turno_id
        Calificacion::create([
            'turno_id'   => $request->turno_id,
            'cliente_id' => auth()->id(),
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', '¡Muchas gracias por dejarnos tu experiencia!');
    }
}