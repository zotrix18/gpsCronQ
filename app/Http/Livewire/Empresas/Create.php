<?php

namespace App\Http\Livewire\Empresas;

use App\Livewire\Forms\EmpresaForm;
use App\Models\Empresa;
use App\Models\IvaCategoria;
use App\Models\User;
use App\Models\MedioPago;
use Livewire\Component;

class Create extends Component
{
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

        $this->dispatch('alertaExito');
        $ivascategorias = IvaCategoria::get();
        $users = User::get();
        $mediospago = MedioPago::all();

        return view('livewire.empresas.create', [
            'ivascategorias' => $ivascategorias,
            'users' => $users,
            'mediospago' => $mediospago
        ]);
    }
}

