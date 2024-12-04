<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'numero',
        'contactos_id',
        'importe_subtotal',
        'importe_iva',
        'importe_descuento',
        'importe_total',
        'estado',
        'observaciones',
    ];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'contactos_id');
    }
}
