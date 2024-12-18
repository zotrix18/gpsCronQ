<?php

namespace App\Http\Livewire\Unidades;

use Carbon\Carbon;
use App\Livewire\Forms\UnidadesForm;
use Livewire\Component;
use Livewire\WithFileUploads;


class Create extends Component{
    use WithFileUploads;
    public $title = "Crear Unidad";
    public UnidadesForm $form;
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

    public function resetJs($value = null){
        $this->dispatch('resetJs', $value);
    }

    public function setImgCustom($file){
        // $this->form->imgCustomPath = $file;
        $this->resetJs();
    }

    public function handleFileUpload(){
        $this->resetJs();
    }

    public function save(){
        $this->form->store();
        $this->dispatch('alertaExito');
        redirect(route('unidades.index'));
    }

    public function render(){
        return view('livewire.unidades.create');
    }
}