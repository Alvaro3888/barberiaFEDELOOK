<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'turno_id',
        'monto',
        'metodo',
        'estado',
        'payment_id_mp',
        'collection_id_mp',
        'preference_id_mp',
        'merchant_order_id_mp',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
}