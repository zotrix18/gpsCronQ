<?php

namespace App\Http\Livewire\MedioPago;

use App\Livewire\Forms\MedioPagoForm;
use App\Models\MedioPago;
use Livewire\Component;

class Form extends Component
{
  public MedioPagoForm $form;
  public $title = "Crear medio de pago";
  public $isUpdate = false;


  public function mount($id = null)
  {
    if ($id) {
      $medio = MedioPago::findOrFail($id);
      $this->form->setMedioPago($medio);
      $this->title = "Editar medio de pago";
      $this->isUpdate = true;
    }
  }

  public function save()
  {
    $this->isUpdate ? $this->form->update() : $this->form->store();

    $message = $this->isUpdate ? "Medio de pago actualizado con éxito." : "Medio de pago creado con éxito.";
    session()->flash('message', $message);
  }

  public function render()
  {
    return view('livewire.mediopago.form');
  }
}