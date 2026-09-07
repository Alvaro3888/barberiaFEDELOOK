<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Turno;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use App\Models\Imagen;

class ServicioController extends Controller
{
    /**
     * Vista pública del index (usuario/index.blade.php).
     */
    public function indexPublic()
    {
        // 🔹 Solo servicios y sucursales activos
        $servicios  = Servicio::where('activo', true)->get();
        $sucursales = Sucursal::where('activo', true)->get();

        $turnos = [];
        if (Auth::check()) {
            $turnos = Turno::where('cliente_id', Auth::id())
                           ->with('servicio')
                           ->orderBy('fecha','asc')
                           ->orderBy('hora','asc')
                           ->get();
        }

        $hero = Imagen::where('tipo','hero')->first();
        $heroImagen = $hero ? $hero->path : asset('images/default_hero.jpg');

        return view('usuario.index', compact('servicios','sucursales','turnos','heroImagen'));
    }

    /**
     * Listado de servicios (panel admin).
     */
    public function index(Request $request)
    {
        $query = Servicio::with('sucursal');

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id',$request->sucursal_id);
        }
        if ($request->filled('categoria')) {
            $query->where('categoria',$request->categoria);
        }

        $servicios   = $query->get();
        $sucursales  = Sucursal::all();
        $categorias  = Servicio::select('categoria')->distinct()->pluck('categoria');

        if ($request->ajax()) {
            return view('admin.servicios.partials.servicios_table', compact('servicios'))->render();
        }

        return view('admin.servicios.index', compact('servicios','sucursales','categorias'));
    }

    public function create()
    {
        return view('admin.servicios.create', ['sucursales'=>Sucursal::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'duracion'      => 'required|integer',
            'precio_actual' => 'required|numeric',
            'imagen'        => 'nullable|image|max:2048',
            'categoria'     => 'required|string|max:50',
            'sucursal_id'   => 'required|exists:sucursales,id',
        ]);

        $servicio = new Servicio($request->only(['nombre','duracion','precio_actual','categoria','sucursal_id']));
        $servicio->activo = true; // 🔹 siempre activo al crear

        if ($request->hasFile('imagen')) {
            $servicio->imagen = $request->file('imagen')->store('servicios','public');
        }

        $servicio->save();

        return redirect()->route('admin.servicios.index')->with('success','Servicio creado correctamente.');
    }

    public function show(string $id)
    {
        return view('admin.servicios.show',['servicio'=>Servicio::findOrFail($id)]);
    }

    public function edit(string $id)
    {
        return view('admin.servicios.edit',[
            'servicio'=>Servicio::findOrFail($id),
            'sucursales'=>Sucursal::all()
        ]);
    }

    public function update(Request $request,string $id)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'duracion'      => 'required|integer',
            'precio_actual' => 'required|numeric',
            'imagen'        => 'nullable|image|max:2048',
            'categoria'     => 'required|string|max:50',
            'sucursal_id'   => 'required|exists:sucursales,id',
        ]);

        $servicio = Servicio::findOrFail($id);
        $servicio->fill($request->only(['nombre','duracion','precio_actual','categoria','sucursal_id']));

        if ($request->hasFile('imagen')) {
            $servicio->imagen = $request->file('imagen')->store('servicios','public');
        }

        $servicio->save();

        return redirect()->route('admin.servicios.index')->with('success','Servicio actualizado correctamente.');
    }

    // 🔹 Borrado lógico
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = false;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio desactivado correctamente.');
    }

    // 🔹 Recuperar
    public function activate($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = true;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio activado correctamente.');
    }

    /**
     * Vistas públicas de servicios (usuario/servicios/...).
     */
    public function cortes()
    {
        return view('usuario.servicios.cortes',[
            'servicios'=>Servicio::where('categoria','cortes')->where('activo',true)->get()
        ]);
    }

    public function barba()
    {
        return view('usuario.servicios.barba',[
            'servicios'=>Servicio::where('categoria','barba')->where('activo',true)->get()
        ]);
    }

    public function coloracion()
    {
        return view('usuario.servicios.coloracion',[
            'servicios'=>Servicio::where('categoria','coloracion')->where('activo',true)->get()
        ]);
    }
}
