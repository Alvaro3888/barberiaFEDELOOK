<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Muestra la vista de gestión de respaldos y frecuencia.
     */
    public function index()
    {
        $frecuenciaActual = Configuracion::get('backup_frecuencia', '7'); // Por defecto cada 7 días
        
        // Obtenemos también la configuración general si la vista la necesita
        $config = [
            'whatsapp_numero' => Configuracion::get('whatsapp_numero', ''),
            'whatsapp_mensaje' => Configuracion::get('whatsapp_mensaje', ''),
            'instagram_url' => Configuracion::get('instagram_url', ''),
            'facebook_url' => Configuracion::get('facebook_url', ''),
            'tiktok_url' => Configuracion::get('tiktok_url', ''),
        ];

        return view('admin.configuracion.index', compact('frecuenciaActual', 'config'));
    }

    /**
     * Guarda la frecuencia elegida por el administrador.
     */
    public function updateFrecuencia(Request $request)
    {
        $request->validate([
            'backup_frecuencia' => 'required|in:1,3,7,30',
        ]);

        Configuracion::set('backup_frecuencia', $request->backup_frecuencia);

        return redirect()->back()->with('success', 'Frecuencia de respaldo automática actualizada correctamente.');
    }

    /**
     * Genera y descarga una copia de seguridad al instante (botón manual).
     */
    public function descargarAhora()
    {
        try {
            // Ejecutamos el comando para la base de datos
            Artisan::call('backup:run --only-db');

            // Apuntamos directamente a la ruta física donde Spatie guarda los backups en local
            $backupPath = storage_path('app/Laravel'); // O el nombre que use tu config por defecto
            
            // Si no existe esa subcarpeta, buscamos en general dentro de storage/app
            $disk = Storage::disk('local');
            $files = $disk->allFiles();
            
            $zipFiles = array_filter($files, function ($file) {
                return str_ends_with($file, '.zip');
            });

            if (empty($zipFiles)) {
                // Intentamos buscar también en la ruta alternativa si cambió el nombre del directorio raíz del paquete
                $allDiskFiles = glob(storage_path('app/*/*.zip'));
                if (!empty($allDiskFiles)) {
                    usort($allDiskFiles, function($a, $b) {
                        return filemtime($b) <=> filemtime($a);
                    });
                    return response()->download($allDiskFiles[0]);
                }

                return redirect()->back()->with('error', 'El comando se ejecutó pero el archivo zip aún no está disponible. Revisa la consola o storage/app.');
            }

            // Ordenamos por el más reciente
            usort($zipFiles, function ($a, $b) use ($disk) {
                return $disk->lastModified($b) <=> $disk->lastModified($a);
            });

            return $disk->download($zipFiles[0]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el backup: ' . $e->getMessage());
        }
    }
}