<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'contactos_id',
        'numero',
        'listasprecios_id',
        'fecha',
        'fecha_vencimiento',
        'importe_total',
        'importe_apagar',
        'estado',
        'voucher_number',
        'email_envio',
        'puntosventas_id',
        'mediopagos_id',
        'users_id',
    ];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'contactos_id');
    }

    public function listaPrecio()
    {
        return $this->belongsTo(Listaprecio::class, 'listasprecios_id');
    }

    public function puntoVenta()
    {
        return $this->belongsTo(PuntoVenta::class, 'puntosventas_id');
    }

    public function ventaDetalle()
    {
        return $this->hasMany(VentaDetalle::class, 'ventas_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
