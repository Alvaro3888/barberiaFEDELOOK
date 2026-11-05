<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SucursalController extends Controller
{
    public function publicIndex()
    {
        $sucursales = Sucursal::where('activo', 1)->get();

        // Traer únicamente calificaciones activas de 4 o 5 estrellas para mostrarlas en la vista pública
        $testimonios = Calificacion::with(['cliente', 'turno.servicio', 'turno.barbero'])
            ->whereIn('puntuacion', [4, 5])
            ->where('activo', 1) // <--- ESTO ES LO QUE FALTABA
            ->latest('created_at')
            ->get();

        return view('usuario.sucursales.index', compact('sucursales', 'testimonios'));
    }

    public function index(Request $request)
    {
        $query = Sucursal::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('direccion')) {
            $query->where('direccion', 'like', '%' . $request->direccion . '%');
        }

        $sucursales = $query->latest()->get();

        if ($request->ajax()) {
            return view('admin.sucursales.partials.sucursales_table', compact('sucursales'))->render();
        }

        return view('admin.sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('admin.sucursales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono'  => 'required|string|max:50',
            'horarios'  => 'nullable|string|max:255',
            'mapa_url'  => 'nullable|string',
            'imagen'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nombre', 'direccion', 'telefono', 'horarios', 'mapa_url']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('sucursales', 'public');
        }

        Sucursal::create($data);

        return redirect()->route('admin.sucursales.index')
                       ->with('success', 'Sucursal creada correctamente.');
    }

    public function edit($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        return view('admin.sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, $id)
    {
        $sucursal = Sucursal::findOrFail($id);

        $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'telefono'  => 'required|string|max:50',
            'horarios'  => 'nullable|string|max:255',
            'mapa_url'  => 'nullable|string',
            'imagen'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['nombre', 'direccion', 'telefono', 'horarios', 'mapa_url']);

        if ($request->hasFile('imagen')) {
            if ($sucursal->imagen && Storage::disk('public')->exists($sucursal->imagen)) {
                Storage::disk('public')->delete($sucursal->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('sucursales', 'public');
        } else {
            $data['imagen'] = $sucursal->imagen;
        }

        $sucursal->update($data);

        return redirect()->route('admin.sucursales.index')
                       ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->activo = false;
        $sucursal->save();

        return redirect()->route('admin.sucursales.index')
            ->with('success', 'Sucursal desactivada correctamente.');
    }

    public function activate($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->activo = true;
        $sucursal->save();

        return redirect()->route('admin.sucursales.index')
            ->with('success', 'Sucursal activada correctamente.');
    }

    public function enviarContacto(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'telefono'    => 'nullable|string|max:50',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'mensaje'     => 'required|string|max:2000',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required'  => 'El correo electrónico es obligatorio.',
            'email.email'     => 'Ingresá un correo electrónico válido.',
            'mensaje.required'=> 'El mensaje no puede estar vacío.',
        ]);

        $datos = $request->only(['nombre', 'email', 'telefono', 'sucursal_id', 'mensaje']);

        $sucursalNombre = 'Consulta general / Oficina central';
        if (!empty($datos['sucursal_id'])) {
            $sucursal = Sucursal::find($datos['sucursal_id']);
            if ($sucursal) {
                $sucursalNombre = $sucursal->nombre;
            }
        }

        try {
            Mail::send('emails.contacto', [
                'datos' => $datos,
                'sucursalNombre' => $sucursalNombre
            ], function ($message) use ($request) {
                $message->to('fedelookweb@gmail.com')
                        ->subject('📩 Nueva consulta web de cliente - Fede looK');
            });

            return redirect()->back()->with('success', '¡Gracias por comunicarte! Tu mensaje fue enviado con éxito a la administración.');
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo de contacto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo enviar el mensaje en este momento. Por favor intentá nuevamente.');
        }
    }
}