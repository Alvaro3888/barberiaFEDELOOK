<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon; // <--- Asegúrate de importar Carbon arriba

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'password',
        'rol_id',
        'sucursal_id',
        'turno_trabajo_id',
        'activo',
        'avatar',
        'ausencias_online_count', // <--- Agregado (Fase 1)
        'penalizado_hasta',       // <--- Agregado (Fase 1)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relación con Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // Relación con Sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    // Relación con Turnos como cliente
    public function turnosCliente()
    {
        return $this->hasMany(Turno::class, 'cliente_id');
    }

    // Relación con Turnos como barbero
    public function turnosBarbero()
    {
        return $this->hasMany(Turno::class, 'barbero_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'cliente_id');
    }

    // Relación: Un usuario (barbero) tiene asignado un turno de trabajo
    public function turnoTrabajo()
    {
        return $this->belongsTo(TurnoTrabajo::class, 'turno_trabajo_id');
    }

    // Helper de Penalización (Fase 1)
    public function estaPenalizado()
    {
        return $this->penalizado_hasta && Carbon::parse($this->penalizado_hasta)->isFuture();
    }
}