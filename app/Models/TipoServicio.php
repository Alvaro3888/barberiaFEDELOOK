<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    use HasFactory;

    protected $table = 'tipos_servicio';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'sucursal_id'
    ];

    // 🔹 Cada tipo pertenece a una sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // 🔹 Cada tipo tiene muchos servicios
    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}
