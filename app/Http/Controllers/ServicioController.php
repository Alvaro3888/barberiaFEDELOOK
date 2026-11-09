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
     * Vista pública del index principal (usuario/index.blade.php).
     * Carga servicios activos con sus relaciones de sucursales, turnos del usuario autenticado (si aplica)
     * e imágenes configuradas para el banner principal (hero).
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
     * Listado general de servicios en el panel de administración (muestra tanto activos como inactivos).
     * Soporta filtros por sucursal y tipo de servicio, además de respuestas dinámicas vía AJAX para tablas parciales.
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

    /**
     * Muestra el formulario para la creación de un nuevo servicio.
     */
    public function create()
    {
        $sucursales = Sucursal::all();
        $tipos = TipoServicio::where('activo', true)->get();
        return view('admin.servicios.create', compact('sucursales','tipos'));
    }

    /**
     * Almacena un nuevo servicio en la base de datos previa validación estricta de campos.
     * Gestiona opcionalmente la subida y almacenamiento de la imagen ilustrativa en el disco público.
     */
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
        $servicio->activo = true; // Todo servicio nuevo nace activo por defecto

        if ($request->hasFile('imagen')) {
            $servicio->imagen = $request->file('imagen')->store('servicios','public');
        }

        $servicio->save();

        return redirect()->route('admin.servicios.index')->with('success','Servicio creado correctamente.');
    }

    /**
     * Muestra la información detallada de un servicio específico en el panel de administración.
     */
    public function show(string $id)
    {
        return view('admin.servicios.show',['servicio'=>Servicio::with('tipoServicio.sucursal')->findOrFail($id)]);
    }

    /**
     * Carga el formulario de edición con los datos actuales de un servicio.
     */
    public function edit(string $id)
    {
        $servicio   = Servicio::findOrFail($id);
        $sucursales = Sucursal::all();
        $tipos      = TipoServicio::where('activo', true)->get();
        return view('admin.servicios.edit', compact('servicio','sucursales','tipos'));
    }

    /**
     * Actualiza la información de un servicio existente.
     * Si se adjunta una nueva imagen, elimina la anterior del almacenamiento público para evitar archivos huérfanos.
     */
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

    /**
     * Realiza una baja lógica (Soft-Delete) de un servicio marcándolo como inactivo (activo = false).
     */
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = false;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio desactivado correctamente.');
    }

    /**
     * Reactiva un servicio previamente dado de baja lógica (activo = true).
     */
    public function activate($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->activo = true;
        $servicio->save();

        return redirect()->route('admin.servicios.index')
            ->with('success', 'Servicio activado correctamente.');
    }

    /**
     * Vistas públicas de servicios filtradas por la categoría de "Cortes" y estrictamente activos.
     */
    public function cortes()
    {
        return view('usuario.servicios.cortes',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','cortes'))
                                 ->where('activo',true)->get()
        ]);
    }

    /**
     * Vistas públicas de servicios filtradas por la categoría de "Barba" y estrictamente activos.
     */
    public function barba()
    {
        return view('usuario.servicios.barba',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','barba'))
                                 ->where('activo',true)->get()
        ]);
    }

    /**
     * Vistas públicas de servicios filtradas por la categoría de "Coloración" y estrictamente activos.
     */
    public function coloracion()
    {
        return view('usuario.servicios.coloracion',[
            'servicios'=>Servicio::whereHas('tipoServicio', fn($q)=>$q->where('nombre','coloracion'))
                                 ->where('activo',true)->get()
        ]);
    }
}