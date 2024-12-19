<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Livewire\Forms\UnidadesForm;
use App\Models\Unidads;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class Show extends Component{
    use WithFileUploads;
    public $title = "Datos Unidad";
    public UnidadesForm $form;
    public $show = true;
    
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

    public function render(){        
        return view('livewire.unidades.create');
    }
}