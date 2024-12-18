<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Livewire\Forms\UnidadesForm;
use App\Models\Unidads;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class Update extends Component{
    use WithFileUploads;
    public $title = "Actualizar Unidad";
    public UnidadesForm $form;
    public $unidad_id = null;
    public $unidad = null;
    public $currentStore = [
        'imgCustom' => null,
    ];
    public $imgs = [
        ['nombre' => 'Negro', 'path' => 'assets/images/cars/blackCar.png'],
        ['nombre' => 'Azul', 'path' => 'assets/images/cars/blueCar.png'],
        ['nombre' => 'Gris', 'path' => 'assets/images/cars/grayCar.png'],
        ['nombre' => 'Verde', 'path' => 'assets/images/cars/greenCar.png'],
        ['nombre' => 'Naranja', 'path' => 'assets/images/cars/orangeCar.png'],
        ['nombre' => 'Rojo', 'path' => 'assets/images/cars/redCar.png'],
        ['nombre' => 'Celeste', 'path' => 'assets/images/cars/skybluecar.png'],
        ['nombre' => 'Rojo Claro', 'path' => 'assets/images/cars/skyredCar.png'],
        ['nombre' => 'Amarillo', 'path' => 'assets/images/cars/yellowCar.png'],
    ];

    public function mount($id){
        $unidads = Unidads::findOrFail($id);
        $this->form->setUnidad($unidads);        
    }

    //Metodo para reinicio de elementos js
    public function resetJs(){
        $this->dispatch('resetJs');
    }

    public function setImgCustom($file){
        // $this->form->imgCustomPath = $file;
        $this->resetJs();
    }

    public function handleFileUpload($event){
        $file = request()->file('imgCustomPath');
        $this->resetJs();
    }

    public function save(){
        $this->form->update();        
    }

    public function render(){
        return view('livewire.unidades.create');
    }
}