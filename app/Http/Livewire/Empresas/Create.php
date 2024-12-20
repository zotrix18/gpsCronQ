<?php

namespace App\Http\Livewire\Empresas;

use App\Livewire\Forms\EmpresaForm;
use App\Models\Empresa;
use App\Models\IvaCategoria;
use App\Models\User;
use App\Models\MedioPago;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public EmpresaForm $empresa;
    public $title = 'Crear empresa';

    // Método para guardar la empresa
    public function save()
    {
        $this->empresa->store();

        // Mensaje de éxito
        session()->flash('message', 'Usuario creado con éxito.');
    }


    public function render()
    {
        return view('livewire.empresas.create');
    }
}

