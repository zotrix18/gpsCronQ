<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\CategoriaProducto;

class CategoriaForm extends Form
{
    public ?CategoriaProducto $cat;

    #[Validate('required|max:254')]
    public $categoriasproducto = '';

    public function setCategoria(CategoriaProducto $categoria) {
        $this->cat = $categoria;

        $this->categoriasproducto = $this->cat->categoriasproducto;
    }

    public function store() {
        $this->validate();

        $categoria = new CategoriaProducto();
        $categoria->categoriasproducto = $this->categoriasproducto;
        $categoria->empresas_id = session('empresa')->id;
        $categoria->activo = 1;
        
        $categoria->save();
    }

    public function update() {
        $this->validate();

        $categoria = CategoriaProducto::find($this->cat->id);
        $categoria->categoriasproducto = $this->categoriasproducto;

        $categoria->update();
    }
}
