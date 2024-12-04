<?php

namespace App\Http\Livewire\Categorias;

use Livewire\Component;

use App\Livewire\Forms\CategoriaForm;

use App\Models\CategoriaProducto;

class Update extends Component
{
    public $title = 'Actualizar Categoría';
    public $subtitle = 'Actualización';
    public CategoriaForm $form;

    public function mount($id)
    {
        $categoria = CategoriaProducto::findOrFail($id);
        $this->form->setCategoria($categoria);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Categoría actualizada con éxito.');
    }

    public function render()
    {
        return view('livewire.categorias.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
