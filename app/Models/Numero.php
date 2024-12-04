<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Numero extends Model
{
    use HasFactory;
    protected $fillable = [
        'identificador',
        'numero',
        'empresas_id',
        'assistant_id',
        'activo',
    ];
}
