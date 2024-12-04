<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracions extends Model
{
    use HasFactory;

    protected $fillable = [
        'configuracion',
        'texto',
        'descripcion',
        'estado'
    ];
}
