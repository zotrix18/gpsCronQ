<?php

namespace App\Http\Livewire\Empresas;

use App\Livewire\Forms\EmpresaForm;
use App\Models\IvaCategoria;
use App\Models\User;
use Livewire\Component;
use App\Models\Empresa;
use App\Models\MedioPago;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;
    public EmpresaForm $empresa;
    public $title = 'Actualizar empresa';

    public function mount($id)
    {
        $empresa = Empresa::findOrFail($id);
        $this->empresa->setEmpresa($empresa);
    }

    // Método para guardar la empresa
    public function save()
    {
        $this->empresa->update();

        // Mensaje de éxito
        session()->flash('message', 'Empresa actualizada con éxito.');
    }


    public function render()
    {
        $users = User::get();

        return view('livewire.empresas.create', [
            'users' => $users,
        ]);
    }
}

