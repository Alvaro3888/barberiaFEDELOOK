<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'activo',
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
}
