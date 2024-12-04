<?php

namespace App\Livewire\Forms;

use App\Models\MedioPago;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MedioPagoForm extends Form
{
  public ?MedioPago $mediopago;

  #[Validate('required|string|max:254')]
  public $nombre = '';

  // #[Validate('boolean')]
  // public $activo = false;

  public function setMedioPago(MedioPago $mediopago)
  {
    $this->mediopago = $mediopago;

    $this->nombre = $mediopago->nombre;
    // $this->activo = $mediopago->activo;
  }

  public function store()
  {
    // $this->activo = true;
    $this->validate();

    MedioPago::create($this->all());

    $this->reset();
  }

  public function update()
  {
    $this->validate();

    $this->mediopago->update(
      $this->all()
    );
  }
}