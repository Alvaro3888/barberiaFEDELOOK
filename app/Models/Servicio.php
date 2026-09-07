<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
     protected $fillable = [
        'nombre',
        'duracion',
        'precio_actual',
        'categoria',
        'sucursal_id',
        'imagen',
        'activo' // 🔹 agregado para borrado lógico
    ];

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function imagenes()
    {
        return $this->hasMany(\App\Models\Imagen::class, 'referencia_id')
                    ->where('tipo','servicio');
    }
}
