<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\ProductoStock;

class ProductoStocksForm extends Form
{
    #[Validate('required')]
    public $producto_id = '';

    #[Validate('required')]
    public $depositos_id = '';

    #[Validate('required|numeric|gt:0')]
    public $cantidad = '';

    public function store()
    {
        $this->validate();

        $productostock = new ProductoStock;
        $productostock->productos_id = $this->producto_id;
        $productostock->depositos_id = $this->depositos_id;
        $productostock->cantidad = $this->cantidad;

        $productostock->save();

        $this->reset();
    }
}
