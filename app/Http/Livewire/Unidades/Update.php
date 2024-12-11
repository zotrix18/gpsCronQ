<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Livewire\Forms\UnidadesForm;
use App\Models\Unidads;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Update extends Component{
    public $title = "Actualizar Unidad";
    public UnidadesForm $form;
    public $unidad_id = null;
    public $unidad = null;

    public function mount($id){
        $unidads = Unidads::findOrFail($id);
        $this->form->setUnidad($unidads);        
    }

    public function save(){
        $this->form->update();        
    }

    public function render(){
        return view('livewire.unidades.create');
    }
}