<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'contacto',
        'cuit',
        'ivascategoria_id',
        'dirección',
        'ciudad',
        'telefono',
        'email',
        'activo',
        'listasprecios_id',
    ];
}
