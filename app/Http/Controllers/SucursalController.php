<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $query = Sucursal::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('direccion')) {
            $query->where('direccion', 'like', '%' . $request->direccion . '%');
        }

        $sucursales = $query->get();

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
        ]);

        Sucursal::create($request->all());

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
        ]);

        $sucursal->update($request->all());

        return redirect()->route('admin.sucursales.index')
                         ->with('success', 'Sucursal actualizada correctamente.');
    }

    // 🔹 Borrado lógico
public function destroy($id)
{
    $sucursal = Sucursal::findOrFail($id);
    $sucursal->activo = false;
    $sucursal->save();

    return redirect()->route('admin.sucursales.index')
        ->with('success', 'Sucursal desactivada correctamente.');
}

// 🔹 Recuperar
public function activate($id)
{
    $sucursal = Sucursal::findOrFail($id);
    $sucursal->activo = true;
    $sucursal->save();

    return redirect()->route('admin.sucursales.index')
        ->with('success', 'Sucursal activada correctamente.');
}

}
