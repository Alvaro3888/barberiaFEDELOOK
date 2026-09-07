<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    /**
     * Mostrar listado de imágenes (opcional, para panel admin).
     */
    public function index()
    {
        $imagenes = Imagen::all();
        return view('admin.imagenes.index', compact('imagenes'));
    }

    /**
     * Guardar nueva imagen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string', // hero, servicio, sucursal
            'referencia_id' => 'nullable|integer',
            'archivo' => 'nullable|image|max:2048',
            'url' => 'nullable|url'
        ]);

        $ruta = null;

        // Si subió archivo interno
        if ($request->hasFile('archivo')) {
            $ruta = $request->file('archivo')->store('imagenes', 'public');
        }

        Imagen::create([
            'tipo' => $request->tipo,
            'referencia_id' => $request->referencia_id,
            'ruta' => $ruta,
            'url' => $request->url
        ]);

        return redirect()->back()->with('success', 'Imagen creada correctamente.');
    }

    /**
     * Actualizar imagen existente.
     */
    public function update(Request $request, Imagen $imagen)
    {
        $request->validate([
            'tipo' => 'required|string',
            'referencia_id' => 'nullable|integer',
            'archivo' => 'nullable|image|max:2048',
            'url' => 'nullable|url'
        ]);

        $ruta = $imagen->ruta;

        // Si subió nuevo archivo, reemplazamos
        if ($request->hasFile('archivo')) {
            if ($ruta) {
                Storage::disk('public')->delete($ruta);
            }
            $ruta = $request->file('archivo')->store('imagenes', 'public');
        }

        $imagen->update([
            'tipo' => $request->tipo,
            'referencia_id' => $request->referencia_id,
            'ruta' => $ruta,
            'url' => $request->url
        ]);

        return redirect()->back()->with('success', 'Imagen actualizada correctamente.');
    }

    /**
     * Eliminar imagen.
     */
    public function destroy(Imagen $imagen)
    {
        if ($imagen->ruta) {
            Storage::disk('public')->delete($imagen->ruta);
        }
        $imagen->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}
