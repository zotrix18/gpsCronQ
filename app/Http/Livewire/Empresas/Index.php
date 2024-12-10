<?php

namespace App\Http\Livewire\Empresas;

use App\Models\Empresa;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title;
    public $querySearch = ''; // Propiedad para almacenar la consulta de búsqueda




    public function mount()
    {
        $this->title = "Listado de empresas";
    }

    public function updatingQuerySearch()
    {
        // Reinicia la paginación cada vez que cambia el término de búsqueda

        $this->resetPage();

    }

    public function confirmarCambioEstado($id)
    {
        $this->dispatch('confirmarCambioEstado', id: $id);
    }

    // Método público para cambiar el estado
    #[On('cambiarEstado')]
    public function cambiarEstado($id)
    {
        $empresa = Empresa::find($id);
        if ($empresa) {
            $empresa->activo = !$empresa->activo;
            $empresa->save();
            $this->dispatch('alertaExito', ['message' => 'El estado se ha actualizado con éxito']);
        }
    }

    public function render()
    {
        // Filtra las empresas según el término de búsqueda

        $empresas = Empresa::where('empresa', 'like', '%' . $this->querySearch . '%')
            ->orWhere('empresa', 'like', '%' . $this->querySearch . '%')
            ->orWhere('key', 'like', '%' . $this->querySearch . '%')
            ->orderByDesc('id')
            ->paginate(10);



        return view('livewire.empresas.index', [
            'empresas' => $empresas
        ]);
    }
}
