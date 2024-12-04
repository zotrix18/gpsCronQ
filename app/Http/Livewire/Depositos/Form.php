<?php

namespace App\Http\Livewire\Depositos;

use App\Livewire\Forms\DepositoForm;
use App\Models\Deposito;
use Livewire\Component;

class Form extends Component
{

    public DepositoForm $form;
    public $title = "Crear deposito";
    public $isUpdate = false;

    public function mount($id = null)
    {
        // si recibimos un id significa que estamos editando el deposito
        if ($id) {
            $deposito = Deposito::findOrFail($id);
            $this->form->setDeposito($deposito);
            $this->title = "Editar depósito";
            $this->isUpdate = true;
        }
    }

    public function save()
    {
        $this->isUpdate ? $this->form->update() : $this->form->store();

        // Mensaje de éxito
        $message = $this->isUpdate ? "Deposito actualizado con éxito." : "Deposito creado con éxito.";
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.depositos.form');
    }
}
