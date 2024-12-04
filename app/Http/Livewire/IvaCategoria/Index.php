<?php

namespace App\Http\Livewire\IvaCategoria;

use App\Models\Deposito;
use App\Models\IvaCategoria;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    public $title = "Listado de iva categoría";
    public $querySearch = ''; // Propiedad para almacenar la consulta de búsqueda

    public function updatingQuerySearch()
    {
        // Reinicia la paginación cada vez que cambia el término de búsqueda
        $this->resetPage();
    }

    public function showConfirmar($id)
    {
        $this->dispatch('showConfirmar', id: $id);
    }


    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $iva = IvaCategoria::findOrFail($id);
        if ($iva) {
            $iva->activo = !$iva->activo;
            $iva->save();
            $this->dispatch('successAlert');
        }
    }

    public function render()
    {
        $ivascategoria = IvaCategoria::where('ivascategoria', 'like', '%' . $this->querySearch . '%')
            ->orWhere('iva', 'like', '%' . $this->querySearch . '%')
            ->orderByDesc('id')
            ->paginate(10);


        return view('livewire.ivacategoria.index', ["ivascategoria" => $ivascategoria]);
    }
}
