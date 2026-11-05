<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Turno;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use App\Models\Imagen;
use App\Models\TipoServicio;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    /**
     * Vista pública del index (usuario/index.blade.php).
     */
    public function indexPublic()
    {
        $servicios  = Servicio::where('activo', true)->with('tipoServicio.sucursal')->get();
        $sucursales = Sucursal::where('activo', true)->get();

        $turnos = [];
        if (Auth::check()) {
            $turnos = Turno::where('cliente_id', Auth::id())
                           ->with('servicio.tipoServicio.sucursal')
                           ->orderBy('fecha','asc')
                           ->orderBy('hora','asc')
                           ->get();
        }

        $hero = Imagen::where('tipo','hero')->first();
        $heroImagen = $hero ? $hero->path : asset('images/default_hero.jpg');

        return view('usuario.index', compact('servicios','sucursales','turnos','heroImagen'));
    }

    /**
     * Listado de servicios (panel admin - Muestra activos e inactivos).
     */
    public function index(Request $request)
    {
        $query = Servicio::with(['tipoServicio.sucursal']);

        if ($request->filled('sucursal_id')) {
            $query->whereHas('tipoServicio', fn($q) => $q->where('sucursal_id', $request->sucursal_id));
        }
        if ($request->filled('tipo_servicio_id')) {
            $query->where('tipo_servicio_id',$request->tipo_servicio_id);
        }

        $servicios   = $query->get();
        $sucursales  = Sucursal::all();
        $tipos       = TipoServicio::all();

        if ($request->ajax()) {
            return view('admin.servicios.partials.servicios_table', compact('servicios'))->render();
        }

        return view('admin.servicios.index', compact('servicios','sucursales','tipos'));
    }

    public function create()
    {
        $sucursales = Sucursal::all();
        $tipos = TipoServicio::where('activo', true)->get();
        return view('admin.servicios.create', compact('sucursales','tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'            => 'required|string|max:255',
            'duracion'          => 'required|integer',
            'precio_actual'     => 'required|numeric',
            'imagen'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tipo_servicio_id'  => 'required|exists:tipos_servicio,id',
        ]);

        $servicio = new Servicio($request->only([
            'nombre','duracion','precio_actual','tipo_servicio_id'
        ]));
        $servicio->activo = true;

        if ($request->hasFile('imagen')) {
            $servicio->imagen = $request->file('imagen')->store('servicios','public');
        }

        $servicio->save();

        return redirect()->route('admin.servicios.index')->with('success','Servicio creado correctamente.');
    }

    public function show(string $id)
    {
        return view('admin.servicios.show',['servicio'=>Servicio::with('tipoServicio.sucursal')->findOrFail($id)]);
    }

    public function edit(string $id)
    {
        $servicio   = Servicio::findOrFail($id);
        $sucursales = Sucursal::all();
        $tipos      = TipoServicio::where('activo', true)->get();
        return view('admin.servicios.edit', compact('servicio','sucursales','tipos'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre'            => 'required|string|max:255',
            'duracion'          => 'required|integer',
            'precio_actual'     => 'required|numeric',
            'imagen'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tipo_servicio_id'  => 'required|exists:tipos_servicio,id',
        ]);

        $servicio = Servicio::findOrFail($id);
        
        $servicio->fill($request->only([
            'nombre','duracion','precio_actual','tipo_servicio_id'
        ]));

        if ($request->hasFile('imagen')) {
            if ($servicio->imagen && Storage::disk('public')->exists($servicio->imagen)) {
                Storage::disk('public')->delete($servicio->imagen);
            }
            $servicio->imagen = $request->file('imagen')->store('servicios','public');
        }

        $servicio->save();

        return redirect()->route('admin.servicios.index')->with('success','Servicio actualizado correctamente.');
    }

    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = false;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio desactivado correctamente.');
    }

    public function activate($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = true;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio activado correctamente.');
    }

    /**
     * Vistas públicas de servicios filtradas por tipo y estrictamente activos.
     */
    public function cortes()
    {
        return view('usuario.servicios.cortes',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','cortes'))
                                 ->where('activo',true)->get()
        ]);
    }

    public function barba()
    {
        return view('usuario.servicios.barba',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','barba'))
                                 ->where('activo',true)->get()
        ]);
    }

    public function coloracion()
    {
        return view('usuario.servicios.coloracion',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','coloracion'))
                                 ->where('activo',true)->get()
        ]);
    }
}