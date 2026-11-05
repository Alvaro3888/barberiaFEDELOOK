<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracions';

    protected $fillable = [
        'clave',
        'valor',
    ];

    /**
     * Helper estático para obtener un valor rápido por clave.
     * Ejemplo: Configuracion::get('whatsapp', '3881234567');
     */
    public static function get($clave, $default = null)
    {
        $config = self::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Helper estático para guardar o actualizar un valor por clave.
     */
    public static function set($clave, $valor)
    {
        return self::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor]
        );
    }
}