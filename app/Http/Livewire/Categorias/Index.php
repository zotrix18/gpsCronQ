<?php

namespace App\Http\Livewire\Categorias;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

use App\Models\CategoriaProducto;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title = "Listado de categorías";
    public $querySearch = '';

    public function showConfirmar($id)
    {
        $this->dispatch('showConfirmar', id: $id);
    }


    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $categoria = CategoriaProducto::findOrFail($id);
        if ($categoria) {
            $categoria->activo = !$categoria->activo;
            $categoria->update();

            $this->dispatch('successAlert');
        }
    }

    public function render()
    {
        $categorias = CategoriaProducto::where('categoriasproducto', 'like', '%' . $this->querySearch . '%')
                                        ->where('empresas_id', session('empresa')->id)
                                        ->orderBy('id', 'desc')
                                        ->paginate(10);

        return view('livewire.categorias.index', [
            'categorias' => $categorias
        ]);
    }
}
