<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoriasproductos_id',
        'unidadsmedidas_id',
        'producto',
        'descripcion',
        'precio',
        'precio_ultimocosto',
        'empresas_id',
        'tipo',
        'activo'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoriasproductos_id', 'id');
    }

    public function unidadmedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidadsmedidas_id', 'id');
    }

    public function compradetalle()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    public function productostocks()
    {
        return $this->hasMany(ProductoStock::class, 'productos_id', 'id');
    }
}
