<?php

namespace App\Http\Livewire\Puntosventa;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

use App\Models\PuntoVenta;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title = "Listado de puntos de venta";
    public $querySearch = '';

    public function showConfirmar($id)
    {
        $this->dispatch('showConfirmar', id: $id);
    }


    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $puntoventa = PuntoVenta::findOrFail($id);
        if ($puntoventa) {
            $puntoventa->activo = !$puntoventa->activo;
            $puntoventa->update();

            $this->dispatch('successAlert');
        }
    }

    public function render()
    {
        $puntosventa = PuntoVenta::where('puntosventa', 'like', '%' . $this->querySearch . '%')
                                    ->where('empresas_id', session('empresa')->id)
                                    ->orderBy('id', 'desc')
                                    ->paginate(10);

        return view('livewire.puntosventa.index', [
            'puntosventa' => $puntosventa,
            'title' => $this->title
        ]);
    }
}
