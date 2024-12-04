<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\Producto;

class ProductosForm extends Form
{
    public ?Producto $prod;

    #[Validate('required')]
    public $producto = '';
 
    #[Validate('required')]
    public $descripcion = '';

    #[Validate('required|int|gt:0')]
    public $precio = '';

    #[Validate('nullable|decimal:2|gt:0')]
    public $precio_ultimocosto = '';

    #[Validate('required')]
    public $categoriasproductos_id = '';

    #[Validate('nullable')]
    public $unidadsmedidas_id = '';

    #[Validate('required')]
    public $tipo = '';
    
    public function setProducto(Producto $producto) {
        $this->prod = $producto;

        $this->producto = $this->prod->producto;
        $this->descripcion = $this->prod->descripcion;
        $this->precio = $this->prod->precio;
        $this->precio_ultimocosto = $this->prod->precio_ultimocosto;
        $this->categoriasproductos_id = $this->prod->categoriasproductos_id;
        $this->unidadsmedidas_id = $this->prod->unidadsmedidas_id;
        $this->empresas_id = $this->prod->empresas_id;
        $this->tipo = $this->prod->tipo;
    }

    public function store() {
        $this->validate();
        
        $producto = new Producto();
        $producto->producto = $this->producto;
        $producto->descripcion = $this->descripcion;
        $producto->precio = $this->precio;
        $producto->precio_ultimocosto = $this->precio_ultimocosto;
        $producto->categoriasproductos_id = $this->categoriasproductos_id;
        $producto->unidadsmedidas_id = $this->unidadsmedidas_id;
        $producto->empresas_id = session('empresa')->id;
        $producto->tipo = $this->tipo;
        $producto->activo = 1;

        $producto->save();
    }

    public function update() {
        $this->validate();

        $producto = Producto::findOrFail($this->prod->id);
        $producto->producto = $this->producto;
        $producto->descripcion = $this->descripcion;
        $producto->precio = $this->precio;
        $producto->precio_ultimocosto = $this->precio_ultimocosto;
        $producto->categoriasproductos_id = $this->categoriasproductos_id;
        $producto->unidadsmedidas_id = $this->unidadsmedidas_id;
        $producto->empresas_id = session('empresa')->id;
        $producto->tipo = $this->tipo;

        $producto->update();
    }
}
