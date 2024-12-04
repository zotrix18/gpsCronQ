<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentaDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventasdetalles';

    protected $fillable = [
        'ventas_id',
        'cantidad',
        'productos_id',
        'depositos_id',
        'precio_unitario',
        'porcentaje_iva',
        'importe_iva',
        'porcentaje_bonificacion',
        'importe_bonificacion',
        'subtotal',
        'total',
        'detalle',
        'observaciones',
    ];

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ventas_id');
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
