<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoServicio;
use App\Models\Sucursal;

class TipoServicioController extends Controller
{
    /**
     * Muestra el listado general de tipos de servicio en el panel de administración
     * (incluyendo tanto activos como inactivos) cargando la relación con su sucursal.
     */
    public function index()
    {
        $tipos = TipoServicio::with('sucursal')->get();
        return view('admin.TipoServicios.index', compact('tipos'));
    }

    /**
     * Muestra el formulario de creación de un nuevo tipo de servicio,
     * listando únicamente las sucursales que se encuentran activas.
     */
    public function create()
    {
        $sucursales = Sucursal::where('activo', true)->get();
        return view('admin.TipoServicios.create', compact('sucursales'));
    }

    /**
     * Valida y almacena un nuevo tipo de servicio en la base de datos,
     * asignándolo a una sucursal y marcándolo como activo por defecto.
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
     * Muestra los detalles de un tipo de servicio específico en el panel administrativo.
     */
    public function show($id)
    {
        $tipo = TipoServicio::with('sucursal')->findOrFail($id);
        return view('admin.TipoServicios.show', compact('tipo'));
    }

    /**
     * Muestra el formulario de edición con los datos actuales de un tipo de servicio
     * y las sucursales activas disponibles para reasignación.
     */
    public function edit($id)
    {
        $tipo       = TipoServicio::findOrFail($id);
        $sucursales = Sucursal::where('activo', true)->get();
        return view('admin.TipoServicios.edit', compact('tipo','sucursales'));
    }

    /**
     * Valida y actualiza la información de un tipo de servicio existente.
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
     * Realiza un borrado lógico (Soft-Delete operativo) de un tipo de servicio 
     * cambiando su estado a inactivo (activo = false).
     */
    public function destroy($id)
    {
        $tipo = TipoServicio::findOrFail($id);
        $tipo->update(['activo' => false]);

        return redirect()->route('admin.TipoServicios.index')
                         ->with('success','Tipo de servicio desactivado correctamente.');
    }

    /**
     * Reactiva un tipo de servicio previamente dado de baja lógica (activo = true).
     */
    public function activate($id)
    {
        $tipo = TipoServicio::findOrFail($id);
        $tipo->update(['activo' => true]);

        return redirect()->route('admin.TipoServicios.index')
                     ->with('success','Tipo de servicio activado correctamente.');
    }

    /**
     * Endpoint API / AJAX: Retorna en formato JSON los tipos de servicio pertenecientes 
     * a una sucursal específica que estén activos (utilizado para selects dinámicos en cascada).
     */
    public function porSucursal($sucursalId)
    {
        $tipos = TipoServicio::where('sucursal_id', $sucursalId)
                             ->where('activo', true)
                             ->get(['id','nombre']);
        return response()->json($tipos);
    }

    /**
     * Vista pública: Muestra el detalle de un tipo de servicio activo específico 
     * junto con todos los servicios activos que le pertenecen.
     */
    public function showPublic($id)
    {
        // Busca el tipo de servicio asegurándose de que esté activo
        $tipo = \App\Models\TipoServicio::where('id', $id)
                                    ->where('activo', true)
                                    ->firstOrFail();

        // Carga todos los servicios activos asociados a este tipo de servicio
        $servicios = \App\Models\Servicio::where('tipo_servicio_id', $id)
                                   ->where('activo', true)
                                   ->get();

        return view('usuario.tiposervicios.show', compact('tipo', 'servicios'));
    }
}