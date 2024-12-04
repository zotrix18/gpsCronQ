<?php

namespace App\Http\Livewire\Productos;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

use App\Models\Producto;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $title = "Listado de productos";
    public $querySearch = '';

    public function showConfirmar($id)
    {
        $this->dispatch('showConfirmar', id: $id);
    }


    #[On('toggleActive')]
    public function toggleActive($id)
    {
        $producto = Producto::findOrFail($id);
        if ($producto) {
            $producto->activo = !$producto->activo;
            $producto->update();

            $this->dispatch('successAlert');
        }
    }

    public function render()
    {
        $productos = Producto::where('producto', 'like', '%' . $this->querySearch . '%')
                            ->orWhere('descripcion', 'like', '%' . $this->querySearch . '%')
                            ->where('empresas_id', session('empresa')->id)
                            ->orderBy('id', 'desc')
                            ->paginate(10);

        return view('livewire.productos.index', [
            'title' => $this->title,
            'productos' => $productos
        ]);
    }
}
