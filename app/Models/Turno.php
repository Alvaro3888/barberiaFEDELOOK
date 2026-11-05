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
        'cliente_nombre_manual',
        'barbero_id',
        'servicio_id',
        'sucursal_id',
        'fecha',
        'hora',
        'estado',
        'precio_historico',
        'reprogramaciones_count',
        'origen',                     // <--- Agregado de Fase 1
        'motivo_cancelacion_emergencia', // <--- Agregado de Fase 1
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

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'turno_servicio', 'turno_id', 'servicio_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'turno_id');
    }

    public function calificacion()
    {
        return $this->hasOne(Calificacion::class);
    }

    // Helpers de Origen (Fase 1)
    public function esPagoOnline()
    {
        return $this->origen === 'web';
    }

    public function esMostrador()
    {
        return $this->origen === 'mostrador';
    }
}