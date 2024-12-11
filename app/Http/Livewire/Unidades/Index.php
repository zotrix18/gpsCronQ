<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Models\Unidads;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component{
    public $title = "Unidades";
    public $unidades = [];

    public function mount(){
        $this->loadUnidades();
    }

    public function loadUnidades(){
        $this->unidades = DB::table('unidads')
        ->where('empresas_id', session('empresa')->id)
        ->get();
    }

    public function confirmarCambioEstado($id){
        $this->dispatch('confirmarCambioEstado', ['id' => $id]);
    }

    public function cambiarEstado($id){
        $unidad = Unidads::findOrFail($id);            
        $unidad->activo = !$unidad->activo;
        $unidad->update();       
        $this->loadUnidades(); 
        $this->dispatch('alertaExito');
    }

    public function render(){
        return view('livewire.unidades.index');
    }
}