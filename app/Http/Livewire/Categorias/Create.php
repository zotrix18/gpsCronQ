<?php

namespace App\Http\Livewire\Categorias;

use App\Livewire\Forms\CategoriaForm;
use Livewire\Component;

use App\Models\CategoriaProducto;

class Create extends Component
{
    public $title = "Crear Categoría";
    public $subtitle = "Creación";
    public CategoriaForm $form;

    public function save() {
        $this->form->store();

        session()->flash('message', 'Categoría creado con éxito.');
        $this->reset(); // Limpia los campos
    }

    public function render()
    {
        return view('livewire.categorias.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
