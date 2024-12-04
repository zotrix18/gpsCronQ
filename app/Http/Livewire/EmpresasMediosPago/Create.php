<?php

namespace App\Http\Livewire\EmpresasMediosPago;

use Livewire\Component;
use App\Models\EmpresaMedioPago;
use App\Models\MedioPago;
use App\Livewire\Forms\EmpresasMediosPagoForm;

class Create extends Component
{
    public EmpresasMediosPagoForm $empresamediopago;
    public $empresa_id = '';
    public $title = 'Vincular medio de pago';
    
    public function mount($id) {
        $this->empresa_id = $id;
        $this->empresamediopago->empresa_id = $id;
    }

    public function save() {
        $this->empresamediopago->store();

        // Mensaje de éxito
        session()->flash('message', 'Medio de pago creado con éxito.');
    }

    public function render()
    {
        $mediospago = MedioPago::all();

        return view('livewire.empresas-medios-pago.create', [
            'title' => $this->title,
            'mediospago' => $mediospago
        ]);
    }
}
