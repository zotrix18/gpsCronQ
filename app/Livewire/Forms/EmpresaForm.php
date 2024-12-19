<?php

namespace App\Livewire\Forms;

use App\Models\Empresa;
use App\Models\EmpresaMedioPago;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;

class EmpresaForm extends Form
{
    public ?Empresa $emp;


    #[Validate('required|string|max:254')]
    public $empresa = '';

    #[Validate('required|string|max:125')]
    public $key = '';

    #[Validate('nullable')]
    public $logo = null;

    public function setEmpresa(Empresa $empresa)
    {
        $this->emp = $empresa;

        $this->empresa = $this->emp->empresa;
        $this->key = $this->emp->key;
        $this->logo = $this->emp->logo;
    }

    public function store()
    {
        $this->activo = true;
        $this->validate();

        $newEmpresa = Empresa::create($this->all());


        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->emp->update(
            $this->all()
        );

    }

}