<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\User;
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
        $this->title = "Listado de usuarios";
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
        $usuario = User::find($id);
        if ($usuario) {
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            $this->dispatch('alertaExito');
        }
    }

    public function render()
    {
        // Filtra los usuarios según el término de búsqueda

        $usuarios = User::where('name', 'like', '%' . $this->querySearch . '%')
            ->orWhere('email', 'like', '%' . $this->querySearch . '%')
            ->orWhere('telefono', 'like', '%' . $this->querySearch . '%')
            ->orderByDesc('id')
            ->paginate(10);



        return view('livewire.usuarios.index', [
            'usuarios' => $usuarios
        ]);
    }
}
