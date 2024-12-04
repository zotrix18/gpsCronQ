<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    use HasFactory;

    protected $table = 'puntosventas';

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresas_id');
    }

    public function talonarios()
    {
        return $this->hasMany(Talonario::class, 'puntosventas_id', 'id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'puntosventas_id', 'id');
    }
}
