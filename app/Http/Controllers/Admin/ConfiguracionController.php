<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    /**
     * Muestra la vista de configuración general de la barbería.
     */
    public function index()
    {
        $config = [
            'whatsapp_numero'  => Configuracion::get('whatsapp_numero', ''),
            'whatsapp_mensaje' => Configuracion::get('whatsapp_mensaje', ''),
            'instagram_url'   => Configuracion::get('instagram_url', ''),
            'facebook_url'    => Configuracion::get('facebook_url', ''),
            'tiktok_url'      => Configuracion::get('tiktok_url', ''),
        ];

        return view('admin.configuracion.index', compact('config'));
    }

    /**
     * Actualiza los valores de configuración en la base de datos.
     */
    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_numero'  => 'nullable|string|max:30',
            'whatsapp_mensaje' => 'nullable|string|max:255',
            'instagram_url'   => 'nullable|url|max:255',
            'facebook_url'    => 'nullable|url|max:255',
            'tiktok_url'      => 'nullable|url|max:255',
        ]);

        Configuracion::set('whatsapp_numero', $request->whatsapp_numero);
        Configuracion::set('whatsapp_mensaje', $request->whatsapp_mensaje);
        Configuracion::set('instagram_url', $request->instagram_url);
        Configuracion::set('facebook_url', $request->facebook_url);
        Configuracion::set('tiktok_url', $request->tiktok_url);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}