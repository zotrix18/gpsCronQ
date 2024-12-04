<?php

namespace App\Http\Livewire\EmpresasMediosPago;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

use App\Models\Empresa;
use App\Models\EmpresaMedioPago;
use App\Models\MedioPago;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title = '';
    public $empresa;
    public $querySearch = '';

    public function mount($id)
    {
        $this->empresa = Empresa::find($id);
    }

    public function render()
    {
        $mediospago = EmpresaMedioPago::select('empresasmediopagos.*', 'mediospagos.nombre as mediopago')
                                        ->join('mediospagos', 'mediospagos.id', '=', 'empresasmediopagos.mediospagos_id')
                                        ->where('mediospagos.nombre', 'like', '%' . $this->querySearch . '%')
                                        ->where('empresasmediopagos.empresas_id', $this->empresa->id)
                                        ->paginate(10);

        $this->title = "Medios de pago de ". $this->empresa->empresa;

        return view('livewire.empresas-medios-pago.index', [
            'title' => $this->title,
            'mediospago' => $mediospago,
            'empresa' => $this->empresa
        ]);
    }

    public function showConfirmar($id)
    {
        $this->dispatch('showConfirmar', id: $id);
    }

    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $empresamediopago = EmpresaMedioPago::findOrFail($id);
        if ($empresamediopago) {
            $empresamediopago->activo = !$empresamediopago->activo;
            $empresamediopago->update();

            $this->dispatch('successAlert');
        }
    }
}
