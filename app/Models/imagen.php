<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    // 👇 Forzamos el nombre correcto de la tabla
    protected $table = 'imagenes';

    protected $fillable = [
        'tipo',
        'referencia_id',
        'ruta',
        'url',
    ];

    // Accesor para devolver la ruta o la URL
    public function getPathAttribute()
    {
        if ($this->ruta) {
            return asset('storage/' . $this->ruta);
        }
        return $this->url;
    }
}
