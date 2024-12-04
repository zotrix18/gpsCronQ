<?php

namespace App\Livewire\Forms;

use App\Models\Deposito;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DepositoForm extends Form
{
    public ?Deposito $dep;

    #[Validate('required|string|max:254')]
    public $deposito = '';

    #[Validate('nullable|string')]
    public $observaciones = '';

    #[Validate('boolean')]
    public $activo = false;

    public function setDeposito(Deposito $deposito)
    {
        $this->dep = $deposito;
        $this->deposito = $this->dep->deposito;
        $this->observaciones = $deposito->observaciones;
        $this->activo = $this->dep->activo;
    }


    public function store()
    {
        $this->activo = true;
        $this->validate();

        Deposito::create($this->all());

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->dep->update(
            $this->all()
        );
    }
}
