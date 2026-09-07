<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turnos';

    protected $fillable = [
        'cliente_id',
        'barbero_id',
        'servicio_id',
        'sucursal_id',
        'fecha',
        'hora',
        'estado',
        'precio_historico',
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }

    public function barbero()
    {
        return $this->belongsTo(Usuario::class, 'barbero_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
