<?php

namespace App\Http\Livewire\IvaCategoria;

use App\Livewire\Forms\IvaCategoriaForm;
use App\Models\IvaCategoria;
use Livewire\Component;

class Form extends Component
{

    public IvaCategoriaForm $form;
    public $title = "Crear iva categoría";
    public $isUpdate = false;

    public function mount($id = null)
    {
        // si recibimos un id significa que estamos editando el deposito
        if ($id) {
            $iva = IvaCategoria::findOrFail($id);
            $this->form->setIvaCategoria($iva);
            $this->title = "Editar iva categoría";
            $this->isUpdate = true;
        }
    }

    public function save()
    {
        $this->isUpdate ? $this->form->update() : $this->form->store();

        // Mensaje de éxito
        $message = $this->isUpdate ? "Iva categoría actualizado con éxito." : "Iva categoría creado con éxito.";
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.ivacategoria.form');
    }
}
