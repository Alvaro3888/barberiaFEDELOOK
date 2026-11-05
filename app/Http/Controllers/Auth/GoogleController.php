<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Procesa la respuesta de Google.
     */
   public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Buscamos si ya existe el correo en la tabla usuarios
            $user = Usuario::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Si no existe, creamos la cuenta asignando rol_id 3 y la foto de Google
                $user = Usuario::create([
                    'nombre'   => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'rol_id'   => 3,
                    'avatar'   => $googleUser->getAvatar(),
                ]);
            } else {
                // Si ya existe, actualizamos su nombre y su avatar de Google por si cambió
                $user->update([
                    'nombre' => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Iniciar sesión
            Auth::login($user);

            // Redirigimos al perfil del cliente
            return redirect()->route('perfil.index');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Error de autenticación con Google: ' . $e->getMessage()]);
        }
    }
}