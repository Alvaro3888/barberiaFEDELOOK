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
    public function index()
    {
        $user = Auth::user();
        $tipos = TipoServicio::all();
        
        return view('perfil.index', compact('user', 'tipos'));
    }

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

    // --- AJAX: ACTUALIZAR FOTO AL INSTANTE ---
    public function actualizarAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        // Borrar anterior si era local (no de Google)
        if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '¡Foto de perfil actualizada con éxito!',
            'url'     => asset('storage/' . $path)
        ]);
    }

    // --- AJAX: ELIMINAR FOTO AL INSTANTE ---
    public function eliminarAvatar()
    {
        $user = Auth::user();

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

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('perfil.index')->with('success', 'Contraseña modificada con éxito.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password_confirm_delete' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_confirm_delete, $user->password)) {
            return back()->withErrors(['password_confirm_delete' => 'La contraseña ingresada no coincide.']);
        }

        if ($user->avatar && !Str::startsWith($user->avatar, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Tu cuenta ha sido eliminada correctamente.');
    }
}