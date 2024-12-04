<?php

namespace App\Http\Livewire\Productos;

use Illuminate\Support\Facades\DB;

use App\Livewire\Forms\ProductosForm;
use Livewire\Component;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\UnidadMedida;

class Create extends Component
{
    public $title = "Crear producto";
    public $subtitle = "Creación";
    public ProductosForm $form;
    public $selectedProductos = [];

    public function addToList()
    {
        $this->validate([
            'form.producto' => 'required',
            'form.descripcion' => 'required',
            'form.precio' => 'required|numeric|gt:0',
            'form.categoriasproductos_id' => 'required',
            'form.tipo' => 'required'
        ]);

        $this->selectedProductos[] = [
            'producto' => $this->form->producto,
            'descripcion' => $this->form->descripcion,
            'precio' => $this->form->precio,
            'precio_ultimocosto' => $this->form->precio_ultimocosto,
            'categoriasproductos_id' => $this->form->categoriasproductos_id,
            'unidadsmedidas_id' => $this->form->unidadsmedidas_id,
            'tipo' => $this->form->tipo
        ];

        // Limpiar el formulario después de agregar
        $this->form->reset();
    }

    public function removeProduct($index)
    {
        unset($this->selectedProductos[$index]);
        $this->selectedProductos = array_values($this->selectedProductos);
    }
    
    public function save() {
        if (empty($this->selectedProductos)) {
            session()->flash('error', 'Debe agregar al menos un producto');
            return;
        }

        try {
            DB::transaction(function () {
                foreach ($this->selectedProductos as $producto) {
                    Producto::create([
                        'producto' => $producto['producto'],
                        'descripcion' => $producto['descripcion'],
                        'precio' => $producto['precio'],
                        'precio_ultimocosto' => $producto['precio_ultimocosto'],
                        'categoriasproductos_id' => $producto['categoriasproductos_id'],
                        'unidadsmedidas_id' => $producto['unidadsmedidas_id'],
                        'empresas_id' => session('empresa')->id,
                        'tipo' => $producto['tipo'],
                        'activo' => 1
                    ]);
                }
            });

            session()->flash('message', 'Productos creados con éxito.');
            $this->reset(['selectedProductos']);
            $this->form->reset();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear los productos: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categorias = CategoriaProducto::all();
        $unidades = UnidadMedida::all();

        return view('livewire.productos.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'categorias' => $categorias,
            'unidades' => $unidades
        ]);
    }
}
