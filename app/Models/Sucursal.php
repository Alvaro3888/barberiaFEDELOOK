<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'horarios',
        'imagen',
        'mapa_url',
        'activo'
    ];

    // 🔹 Relación con tipos de servicio
    public function tiposServicio()
    {
        return $this->hasMany(TipoServicio::class);
    }

    // 🔹 Relación con servicios directos (si aplica)
    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    // 🔹 Relación con imágenes adicionales
    public function imagenes()
    {
        return $this->hasMany(\App\Models\Imagen::class, 'referencia_id')
                    ->where('tipo', 'sucursal');
    }
}