<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoServicio;
use App\Models\Sucursal;

class TipoServicioController extends Controller
{
    /**
     * Listado de tipos de servicio (todos, activos e inactivos).
     */
    public function index()
    {
        $tipos = TipoServicio::with('sucursal')->get();
        return view('admin.TipoServicios.index', compact('tipos'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $sucursales = Sucursal::where('activo', true)->get();
        return view('admin.TipoServicios.create', compact('sucursales'));
    }

    /**
     * Guardar un nuevo tipo de servicio.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        TipoServicio::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'sucursal_id' => $request->sucursal_id,
            'activo'      => true,
        ]);

        return redirect()->route('admin.TipoServicios.index')
                         ->with('success','Tipo de servicio creado correctamente.');
    }

    /**
     * Mostrar un tipo de servicio.
     */
    public function show($id)
    {
        $tipo = TipoServicio::with('sucursal')->findOrFail($id);
        return view('admin.TipoServicios.show', compact('tipo'));
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $tipo       = TipoServicio::findOrFail($id);
        $sucursales = Sucursal::where('activo', true)->get();
        return view('admin.TipoServicios.edit', compact('tipo','sucursales'));
    }

    /**
     * Actualizar un tipo de servicio.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:sucursales,id',
        ]);

        $tipo = TipoServicio::findOrFail($id);
        $tipo->update($request->only(['nombre','descripcion','sucursal_id']));

        return redirect()->route('admin.TipoServicios.index')
                         ->with('success','Tipo de servicio actualizado correctamente.');
    }

    /**
     * Desactivar (borrado lógico).
     */
    public function destroy($id)
    {
        $tipo = TipoServicio::findOrFail($id);
        $tipo->update(['activo' => false]);

        return redirect()->route('admin.TipoServicios.index')
                         ->with('success','Tipo de servicio desactivado correctamente.');
    }

    /**
     * Activar nuevamente.
     */
    public function activate($id)
{
    $tipo = TipoServicio::findOrFail($id);
    $tipo->update(['activo' => true]);

    return redirect()->route('admin.TipoServicios.index')
                     ->with('success','Tipo de servicio activado correctamente.');
}


    /**
     * Tipos de servicio por sucursal (para combos dinámicos).
     */
    public function porSucursal($sucursalId)
    {
        $tipos = TipoServicio::where('sucursal_id', $sucursalId)
                             ->where('activo', true)
                             ->get(['id','nombre']);
        return response()->json($tipos);
    }
    public function showPublic($id)
{
    // Buscar el tipo de servicio (aunque esté borrado lógico)
    $tipo = \App\Models\TipoServicio::where('id', $id)
                                    ->where('activo', true) // solo activos
                                    ->firstOrFail();

    // Traer todos los servicios activos de este tipo
    $servicios = \App\Models\Servicio::where('tipo_servicio_id', $id)
                                     ->where('activo', true)
                                     ->get();

  return view('usuario.tiposervicios.show', compact('tipo', 'servicios'));

}

}
