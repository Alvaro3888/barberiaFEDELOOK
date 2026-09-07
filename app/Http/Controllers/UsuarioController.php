<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\Rol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with(['rol','sucursal']);

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->filled('rol_id')) {
            $query->where('rol_id', $request->rol_id);
        }

        $usuarios   = $query->get();
        $sucursales = Sucursal::all();
        $roles      = Rol::all();

        if ($request->ajax()) {
            return view('admin.usuarios.partials.usuarios_table', compact('usuarios'))->render();
        }

        return view('admin.usuarios.index', compact('usuarios','sucursales','roles'));
    }

    public function create()
    {
        $sucursales = Sucursal::all();
        $roles      = Rol::all();
        return view('admin.usuarios.create', compact('sucursales','roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'email'       => 'required|email|unique:usuarios,email',
            'telefono'    => 'nullable|string|max:50',
            'password'    => 'required|string|min:6',
            'rol_id'      => 'required|exists:roles,id',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $usuario = new Usuario($request->only(['nombre','email','telefono','rol_id','sucursal_id']));
        $usuario->password = bcrypt($request->password);
        $usuario->activo   = true;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario    = Usuario::findOrFail($id);
        $sucursales = Sucursal::all();
        $roles      = Rol::all();
        return view('admin.usuarios.edit', compact('usuario','sucursales','roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre'      => 'required|string|max:255',
            'email'       => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono'    => 'nullable|string|max:50',
            'password'    => 'nullable|string|min:6',
            'rol_id'      => 'required|exists:roles,id',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $usuario->fill($request->only(['nombre','email','telefono','rol_id','sucursal_id']));

        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }

        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
{
    $usuario = Usuario::findOrFail($id);

    // 🔹 Borrado lógico: marcar como inactivo
    $usuario->activo = false;
    $usuario->save();

    return redirect()->route('admin.usuarios.index')
        ->with('success', 'Usuario desactivado correctamente.');
}
public function activate($id)
{
    $usuario = Usuario::findOrFail($id);
    $usuario->activo = true;
    $usuario->save();

    return redirect()->route('admin.usuarios.index')
        ->with('success', 'Usuario activado correctamente.');
}


}
