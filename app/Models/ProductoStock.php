<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoStock extends Model
{
    use HasFactory;

    protected $table = 'productosstocks';

    protected $fillable = [
        'productos_id',
        'depositos_id',
        'cantidad'
    ];
}
