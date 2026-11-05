<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'duracion',
        'descripcion',
        'precio_actual',
        'activo',            // 🔹 borrado lógico
        'imagen',
        'tipo_servicio_id'   // 🔹 FK hacia tipos_servicio
    ];

    // 🔹 Relación con turnos
    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }

    // 🔹 Cada servicio pertenece a un tipo de servicio
    public function tipoServicio()
    {
        return $this->belongsTo(TipoServicio::class);
    }

    // 🔹 Relación con imágenes
    public function imagenes()
    {
        return $this->hasMany(\App\Models\Imagen::class, 'referencia_id')
                    ->where('tipo','servicio');
    }
}
