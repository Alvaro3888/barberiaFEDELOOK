<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Muestra el listado de clientes registrados en el sistema.
     * Filtra estrictamente por rol_id = 3 (Clientes) e implementa un buscador en vivo por nombre, email o teléfono.
     */
    public function index(Request $request)
    {
        // Filtrar exclusivamente usuarios con rol_id = 3 (Clientes)
        $query = Usuario::where('rol_id', 3);

        // Filtro por búsqueda de texto flexible (nombre, email o teléfono)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%")
                  ->orWhere('telefono', 'like', "%{$busqueda}%");
            });
        }

        $usuarios = $query->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Retorna la vista para la creación manual de un nuevo cliente.
     */
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /**
     * Almacena un nuevo cliente registrado desde el panel de administración o mostrador.
     * Genera una contraseña segura y aleatoria automáticamente por backend sin requerir ingreso manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
        ]);

        // Creamos el cliente de mostrador / web sin pedir contraseñas manuales
        $usuario = new Usuario();
        $usuario->nombre   = $request->nombre;
        $usuario->email    = $request->email;
        $usuario->telefono = $request->telefono;
        
        // Asignamos una clave aleatoria segura por backend 
        // (el cliente luego podrá usar la opción de recuperar contraseña si desea ingresar al portal web)
        $usuario->password = bcrypt(Str::random(16));
        $usuario->rol_id   = 3; // Forzar rol de Cliente por seguridad
        $usuario->activo   = true;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Carga los datos de un cliente específico para su edición (restringido únicamente a rol_id = 3).
     */
    public function edit($id)
    {
        // Asegurar que solo busque si es un cliente legítimo (rol_id = 3)
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);
        
        return view('admin.usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza la información operativa, de contacto y el estado de activación de un cliente.
     * Valida que el email sea único excluyendo al propio usuario actual.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:50',
            'activo'   => 'nullable|boolean',
        ]);

        // Actualización estricta de datos operativos y de contacto (CERO modificación de contraseñas por seguridad)
        $usuario->nombre   = $request->nombre;
        $usuario->email    = $request->email;
        $usuario->telefono = $request->telefono;
        
        if ($request->has('activo')) {
            $usuario->activo = $request->activo;
        }
        
        $usuario->save();

        return redirect()->route('admin.usuarios.index')->with('success', 'Datos del cliente actualizados correctamente.');
    }

    /**
     * Realiza un borrado lógico (Soft-Delete operativo) de un cliente cambiando su estado a inactivo (activo = false).
     * Esto preserva la integridad contable, el historial de turnos pasados y las estadísticas.
     */
    public function destroy($id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);

        // Borrado lógico: marcar como inactivo para preservar la integridad contable y de turnos
        $usuario->activo = false;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }

    /**
     * Reactiva la cuenta de un cliente previamente dado de baja lógica (activo = true).
     */
    public function activate($id)
    {
        $usuario = Usuario::where('rol_id', 3)->findOrFail($id);
        $usuario->activo = true;
        $usuario->save();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Cliente activado correctamente.');
    }
}