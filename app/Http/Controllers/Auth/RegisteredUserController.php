<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Sucursal;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Maneja una solicitud de registro entrante.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:usuarios'],
            'telefono' => ['required', 'string', 'max:15'],
            'password' => ['required', 'min:8'],
        ]);

        $sucursal = Sucursal::first();

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'rol_id' => 3, // Rol fijo de cliente
            'sucursal_id' => $sucursal ? $sucursal->id : 1,
            'activo' => 1,
        ]);

        event(new Registered($usuario));

        Auth::login($usuario);

        // Redirige correctamente a tu ruta existente en web.php
        return redirect()->route('usuario.index');
    }
}