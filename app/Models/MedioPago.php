<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    use HasFactory;

    protected $table = "mediospagos";

    protected $fillable = [
        'nombre',
        'image'
        //'activo',
    ];
}
