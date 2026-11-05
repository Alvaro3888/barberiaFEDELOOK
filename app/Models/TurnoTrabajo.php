<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoTrabajo extends Model
{
    protected $table = 'turnos_trabajo';
    protected $fillable = ['nombre', 'hora_inicio', 'hora_fin', 'incluye_siesta'];

    // Relación: Un turno de trabajo pertenece a muchos usuarios (barberos)
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'turno_trabajo_id');
    }
}