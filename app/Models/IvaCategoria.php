<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvaCategoria extends Model
{
  use HasFactory;

  protected $table = 'ivascategorias';

  protected $fillable = [
    'ivascategoria',
    'activo',
    'iva',
  ];
}
