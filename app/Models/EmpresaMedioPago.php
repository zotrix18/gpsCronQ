<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaMedioPago extends Model
{
    use HasFactory;

    protected $table = 'empresasmediopagos';

    protected $fillable = [
        'url',
        'apikey',
        'payment_type',
        'empresas_id',
        'mediospagos_id'
    ];

    public function medioPago()
    {
        return $this->belongsTo(MedioPago::class, 'mediospagos_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresas_id');
    }
}
