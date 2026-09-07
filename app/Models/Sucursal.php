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
    'activo'
];


    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function imagenes()
    {
        return $this->hasMany(\App\Models\Imagen::class, 'referencia_id')
                    ->where('tipo','sucursal');
    }
}
