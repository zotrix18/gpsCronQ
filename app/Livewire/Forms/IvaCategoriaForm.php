<?php

namespace App\Livewire\Forms;

use App\Models\Deposito;
use App\Models\IvaCategoria;
use Livewire\Attributes\Validate;
use Livewire\Form;

class IvaCategoriaForm extends Form
{
    public ?IvaCategoria $ivacat;

    #[Validate('required|string|max:254')]
    public $ivascategoria = '';

    #[Validate('required|string|max:254')]
    public $iva = '';

    #[Validate('boolean')]
    public $activo = false;

    public function setIvaCategoria(IvaCategoria $ivacategoria)
    {
        $this->ivacat = $ivacategoria;
        $this->ivascategoria = $ivacategoria->ivascategoria;
        $this->iva = $ivacategoria->iva;
        $this->activo = $ivacategoria->activo;

    }


    public function store()
    {
        $this->activo = true;
        $this->validate();

        IvaCategoria::create($this->all());

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->ivacat->update(
            $this->all()
        );
    }
}
