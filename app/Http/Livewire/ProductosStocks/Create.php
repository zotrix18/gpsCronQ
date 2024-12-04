<?php

namespace App\Http\Livewire\ProductosStocks;

use Livewire\Component;
use App\Models\Deposito;
use App\Livewire\Forms\ProductoStocksForm;

class Create extends Component
{
    public ProductoStocksForm $productostocks;
    public $producto_id = '';
    public $title = 'Stock de productos';

    public function mount($id) {
        $this->producto_id = $id;
        $this->productostocks->producto_id = $id;
    }

    public function save() {
        $this->productostocks->store();

        // Mensaje de éxito
        session()->flash('message', 'Stock de producto creado con éxito.');
    }

    public function render()
    {
        $depositos = Deposito::all(); // agregar relacion de empresa con deposito

        return view('livewire.productos-stocks.create', [
            'title' => $this->title,
            'depositos' => $depositos
        ]);
    }
}
