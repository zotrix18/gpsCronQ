<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidads extends Model
{
    use HasFactory;
    protected $fillable = [
        'unidad',
        'codigo',
        'empresas_id',
        'path',
        'primaryPath',
        'observaciones',
        'activo',
    ];
}
