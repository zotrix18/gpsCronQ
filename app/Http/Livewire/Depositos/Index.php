<?php

namespace App\Http\Livewire\Depositos;

use App\Models\Deposito;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{

    public $title = "Listado de depositos";
    public $querySearch = ''; // Propiedad para almacenar la consulta de búsqueda

    public function updatingQuerySearch()
    {
        // Reinicia la paginación cada vez que cambia el término de búsqueda
        $this->resetPage();
    }

    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $deposito = Deposito::findOrFail($id);
        if ($deposito) {
            $deposito->activo = !$deposito->activo;
            $deposito->save();
            $this->dispatch('successAlert', message: "Estado actualizado con éxito!");
        }
    }

    public function render()
    {
        $depositos = Deposito::where('deposito', 'like', '%' . $this->querySearch . '%')
            ->orWhere('observaciones', 'like', '%' . $this->querySearch . '%')
            ->orderByDesc('id')
            ->paginate(10);


        return view('livewire.depositos.index', ["depositos" => $depositos]);
    }
}
