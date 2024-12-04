<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'comprasdetalles';

    protected $fillable = [
        'compras_id',
        'depositos_id',
        'productos_id',
        'productosstocks_id',
        'cantidad',
        'importe_iva',
        'importe_descuento',
        'importe_total',
        'importe_subtotal',
        // 'producto',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compras_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'productos_id');
    }

    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'depositos_id');
    }
}
