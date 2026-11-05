<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'turno_id',
        'cliente_id',
        'puntuacion',
        'comentario',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con el Turno
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Relación con el Cliente (modelo Usuario)
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'cliente_id');
    }
}