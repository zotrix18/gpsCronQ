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

    #[Validate('required|string|max:11|unique:empresas,cuit')]
    public $cuit = '';

    #[Validate('required|string|max:254')]
    public $direccion = '';

    #[Validate('required|string|max:125')]
    public $ciudad = '';

    #[Validate('nullable|string|max:254')]
    public $ingresosbrutos = '';

    #[Validate('boolean')]
    public $activo = false;

    #[Validate('required|exists:users,id')]
    public $users_id = '';

    #[Validate('nullable|string|max:45')]
    public $wee_key = '';

    #[Validate('required|exists:ivascategorias,id')]
    public $ivascategorias_id = '';


    public function setEmpresa(Empresa $empresa)
    {
        $this->emp = $empresa;

        $this->empresa = $this->emp->empresa;
        $this->cuit = $this->emp->cuit;
        $this->direccion = $this->emp->direccion;
        $this->ciudad = $this->emp->ciudad;
        $this->ingresosbrutos = $this->emp->ingresosbrutos;
        $this->activo = $this->emp->activo;
        $this->users_id = $this->emp->users_id;
        $this->wee_key = $this->emp->wee_key;
        $this->ivascategorias_id = $this->emp->ivascategorias_id;

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
        $this->validate([
            'cuit' => [
                'required',
                'string',
                'max:11',
                Rule::unique('empresas', 'cuit')->ignore($this->emp->id),
            ],
        ]);

        $this->emp->update(
            $this->all()
        );

    }

}