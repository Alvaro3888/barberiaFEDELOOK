<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\TurnoTrabajo;
use App\Models\Rol;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    /**
     * Lista únicamente al equipo de Personal (Admins y Barberos: rol_id 1 y 2)
     */
    public function index(Request $request)
    {
        // Filtramos EXCLUSIVAMENTE personal operativo/admin (rol_id IN [1, 2])
        $query = Usuario::with(['rol', 'sucursal', 'turnoTrabajo'])
            ->whereIn('rol_id', [1, 2]);

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%");
            });
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        $barberos = $query->get(); // Contiene el personal (Admins y Barberos)
        $sucursales = Sucursal::where('activo', 1)->get();
        $turnos = TurnoTrabajo::all(); 

        // Colegas válidos: Admins [1] o Barberos [2] que estén activos y tengan turno de trabajo asignado
        $colegasSucursal = Usuario::whereIn('rol_id', [1, 2])
            ->where('activo', 1)
            ->whereNotNull('turno_trabajo_id')
            ->get();

        return view('admin.personal.index', compact('barberos', 'sucursales', 'turnos', 'colegasSucursal'));
    }
    public function create()
    {
        $sucursales = Sucursal::where('activo', 1)->get();
        $turnos = TurnoTrabajo::all();
        // Roles 1 (admin) y 2 (barbero) para asignarlos desde personal
        $rolesPersonal = Rol::whereIn('id', [1, 2])->get();

        return view('admin.personal.create', compact('sucursales', 'turnos', 'rolesPersonal'));
    }

    /**
     * Guarda un nuevo miembro del Personal
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:255',
            'email'            => 'required|email|unique:usuarios,email',
            'telefono'         => 'nullable|string|max:50',
            'password'         => 'required|string|min:6',
            'rol_id'           => 'required|in:1,2', // Solo permite Admin (1) o Barbero (2)
            'sucursal_id'      => 'nullable|exists:sucursales,id',
            'turno_trabajo_id' => 'nullable|exists:turnos_trabajo,id',
        ]);

        $personal = new Usuario();
        $personal->nombre          = $request->nombre;
        $personal->email           = $request->email;
        $personal->telefono        = $request->telefono;
        $personal->password        = bcrypt($request->password);
        $personal->rol_id          = $request->rol_id;
        $personal->sucursal_id     = $request->sucursal_id;
        $personal->turno_trabajo_id = $request->turno_trabajo_id;
        $personal->activo          = true;
        $personal->save();

        return redirect()->route('admin.personal.index')->with('success', 'Personal registrado correctamente.');
    }

    /**
     * Actualiza la sucursal y el turno de trabajo del personal de forma directa
     */
    public function updateTurno(Request $request, $id)
    {
        $request->validate([
            'turno_trabajo_id' => 'nullable|exists:turnos_trabajo,id',
            'sucursal_id'      => 'nullable|exists:sucursales,id',
        ]);

        $personal = Usuario::whereIn('rol_id', [1, 2])->findOrFail($id);
        $personal->turno_trabajo_id = $request->turno_trabajo_id;
        $personal->sucursal_id      = $request->sucursal_id;
        $personal->save();

        return redirect()->route('admin.personal.index')->with('success', 'Datos del personal actualizados correctamente.');
    }

    /**
     * Actualiza de forma dinámica las horas de inicio y fin de una franja horaria / turno
     */
    public function updateTurnoHorario(Request $request, $id)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'hora_inicio' => 'required',
            'hora_fin'    => 'required',
        ]);

        $turno = TurnoTrabajo::findOrFail($id);
        $turno->nombre      = $request->nombre;
        $turno->hora_inicio = $request->hora_inicio;
        $turno->hora_fin    = $request->hora_fin;
        $turno->save();

        return redirect()->route('admin.personal.index')->with('success', 'Franja horaria actualizada correctamente.');
    }

    /**
     * Muestra el formulario para editar un miembro del personal
     */
    public function edit($id)
    {
        $personal = Usuario::whereIn('rol_id', [1, 2])->findOrFail($id);
        $sucursales = Sucursal::where('activo', 1)->get();
        $turnos = TurnoTrabajo::all();
        $rolesPersonal = Rol::whereIn('id', [1, 2])->get();

        return view('admin.personal.edit', compact('personal', 'sucursales', 'turnos', 'rolesPersonal'));
    }

    /**
     * Actualiza los datos completos del personal
     */
    public function update(Request $request, $id)
    {
        $personal = Usuario::whereIn('rol_id', [1, 2])->findOrFail($id);

        $request->validate([
            'nombre'           => 'required|string|max:255',
            'email'            => 'required|email|unique:usuarios,email,' . $personal->id,
            'telefono'         => 'nullable|string|max:50',
            'password'         => 'nullable|string|min:6',
            'rol_id'           => 'required|in:1,2',
            'sucursal_id'      => 'nullable|exists:sucursales,id',
            'turno_trabajo_id' => 'nullable|exists:turnos_trabajo,id',
        ]);

        $personal->nombre          = $request->nombre;
        $personal->email           = $request->email;
        $personal->telefono        = $request->telefono;
        $personal->rol_id          = $request->rol_id;
        $personal->sucursal_id     = $request->sucursal_id;
        $personal->turno_trabajo_id = $request->turno_trabajo_id;

        if ($request->filled('password')) {
            $personal->password = bcrypt($request->password);
        }

        $personal->save();

        return redirect()->route('admin.personal.index')->with('success', 'Personal actualizado correctamente.');
    }

    /**
     * Borrado lógico: Desactiva al miembro del personal
     */
    public function destroy($id)
    {
        $personal = Usuario::whereIn('rol_id', [1, 2])->findOrFail($id);
        $personal->activo = false;
        $personal->save();

        return redirect()->route('admin.personal.index')->with('success', 'Personal desactivado correctamente.');
    }

    /**
     * Reactiva al miembro del personal
     */
    public function activate($id)
    {
        $personal = Usuario::whereIn('rol_id', [1, 2])->findOrFail($id);
        $personal->activo = true;
        $personal->save();

        return redirect()->route('admin.personal.index')->with('success', 'Personal activado correctamente.');
    }
}