<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Filtrar exclusivamente usuarios con rol_id = 3 (Clientes)
        $query = Usuario::where('rol_id', 3);

        // Filtro por búsqueda de texto (nombre o email)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%");
            });
        }

        $usuarios = $query->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        $usuario = new Usuario($request->only(['nombre', 'email', 'telefono']));
        $usuario->password = bcrypt($request->password);
        $usuario->rol_id   = 3; // Forzar que sea cliente por defecto
        $usuario->activo   = true;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function edit($id)
    {
        // Asegurar que solo busque si es un cliente (rol_id = 3)
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);
        
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $usuario->fill($request->only(['nombre', 'email', 'telefono']));

        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);

        // Borrado lógico: marcar como inactivo
        $usuario->activo = false;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }

    public function activate($id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);
        $usuario->activo = true;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Cliente activado correctamente.');
    }
}