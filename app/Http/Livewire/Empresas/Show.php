<?php

namespace App\Http\Livewire\Empresas;

use Livewire\Component;
use App\Models\Empresa;

class Show extends Component
{
    public $title = "Detalles de la empresa";
    public $empresa_id = '';
    public Empresa $empresa;

    public function mount($id)
    {
        $this->empresa_id = $id;
        $this->empresa = Empresa::findOrFail($this->empresa_id);
    }

    public function render()
    {    
        return view('livewire.empresas.show');
    }
}
