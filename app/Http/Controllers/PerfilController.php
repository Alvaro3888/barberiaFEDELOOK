<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Models\TipoServicio;

class PerfilController extends Controller
{
    /**
     * Muestra la vista principal del perfil del usuario autenticado,
     * cargando su información de sesión y los tipos de servicio disponibles.
     */
    public function index()
    {
        $user = Auth::user();
        $tipos = TipoServicio::all();
        
        return view('perfil.index', compact('user', 'tipos'));
    }

    /**
     * Valida y actualiza los datos generales del perfil (nombre, teléfono y correo electrónico),
     * garantizando que el email sea único excluyendo al usuario actual.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'required|email|unique:usuarios,email,' . $user->id,
        ]);

        $user->nombre = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('perfil.index')->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * --- AJAX: ACTUALIZAR FOTO AL INSTANTE ---
     * Procesa la subida asíncrona de una nueva foto de perfil (avatar).
     * Elimina el archivo anterior del disco público solo si es local (evita borrar avatares externos de Google/Socialite).
     */
    public function actualizarAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        // Borrar avatar anterior si era local (excluyendo URLs externas como las de Google)
        if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        // Almacena la nueva imagen en el disco público y actualiza la ruta en el modelo
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '¡Foto de perfil actualizada con éxito!',
            'url'     => asset('storage/' . $path)
        ]);
    }

    /**
     * --- AJAX: ELIMINAR FOTO AL INSTANTE ---
     * Remueve de forma asíncrona el avatar local del usuario y limpia el registro en base de datos.
     */
    public function eliminarAvatar()
    {
        $user = Auth::user();

        // Elimina el archivo físico del disco público si no es un avatar externo
        if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        $user->avatar = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto de perfil removida correctamente.'
        ]);
    }

    /**
     * Modifica la contraseña del usuario autenticado.
     * Valida que la contraseña actual sea correcta mediante Hash y que la nueva cumpla con las políticas de seguridad.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // Verificación estricta de la contraseña actual ingresada
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('perfil.index')->with('success', 'Contraseña modificada con éxito.');
    }

    /**
     * Procesa la baja definitiva y eliminación de la cuenta del usuario.
     * Requiere confirmación de contraseña, limpia archivos asociados (avatar), 
     * desautentica al usuario, invalida la sesión y lo redirige al inicio.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password_confirm_delete' => 'required',
        ]);

        $user = Auth::user();

        // Validación de identidad mediante contraseña para autorizar la eliminación de cuenta
        if (!Hash::check($request->password_confirm_delete, $user->password)) {
            return back()->withErrors(['password_confirm_delete' => 'La contraseña ingresada no coincide.']);
        }

        // Limpieza de archivos de imagen locales si existían
        if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        // Cierre de sesión y eliminación del registro del usuario
        Auth::logout();
        $user->delete();

        // Invalidación de seguridad de la sesión activa
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Tu cuenta ha sido eliminada correctamente.');
    }
}