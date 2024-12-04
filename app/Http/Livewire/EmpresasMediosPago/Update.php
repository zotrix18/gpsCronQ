<?php

namespace App\Http\Livewire\EmpresasMediosPago;

use Livewire\Component;

use App\Models\EmpresaMedioPago;
use App\Models\MedioPago;
use App\Livewire\Forms\EmpresasMediosPagoForm;

class Update extends Component
{
    public EmpresasMediosPagoForm $empresamediopago;
    public $empresa_id = '';
    public $title = 'Actualizar medio de pago de empresa';

    public function mount($emp_id, $empresamediopago_id)
    {
        $this->empresa_id = $emp_id;

        $empresamediopago = EmpresaMedioPago::findOrFail($empresamediopago_id);
        $this->empresamediopago->setEmpresaMedioPago($empresamediopago);
    }

    public function save()
    {
        $this->empresamediopago->update();

        // Mensaje de éxito
        session()->flash('message', 'Medio de pago actualizado con éxito.');
    }

    public function render()
    {
        $mediospago = MedioPago::all();

        return view('livewire.empresas-medios-pago.create', [
            'title' => $this->title,
            'empresa_id' => $this->empresa_id,
            'mediospago' => $mediospago
        ]);
    }
}
