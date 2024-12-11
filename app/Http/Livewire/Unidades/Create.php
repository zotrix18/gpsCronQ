<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Livewire\Forms\UnidadesForm;
use Livewire\Component;


class Create extends Component{

    public $title = "Crear Unidad";
    public UnidadesForm $form;

    public function save(){
        $this->form->store();
        $this->dispatch('alertaExito');
        redirect(route('unidades.index'));
    }

    public function render(){
        return view('livewire.unidades.create');
    }
}