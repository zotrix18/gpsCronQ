<?php

namespace App\Http\Livewire\Productos;

use Livewire\Component;

use App\Livewire\Forms\ProductosForm;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\UnidadMedida;

class Update extends Component
{
    public $title = 'Actualizar Producto';
    public $subtitle = 'Actualización';
    public ProductosForm $form;

    public function mount($id)
    {
        $producto = Producto::findOrFail($id);
        $this->form->setProducto($producto);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Producto actualizado con éxito.');
    }

    public function render()
    {
        $categorias = CategoriaProducto::all();
        $unidades = UnidadMedida::all();

        return view('livewire.productos.update', [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'categorias' => $categorias,
            'unidades' => $unidades
        ]);
    }
}
