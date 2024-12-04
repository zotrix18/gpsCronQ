<?php

namespace App\Http\Livewire\Puntosventa;

use Livewire\Component;
use App\Livewire\Forms\PuntoVentaForm;

use App\Models\PuntoVenta;

class Create extends Component
{
    public $title = 'Crear Punto de Venta';
    public $subtitle = 'Creación';
    public PuntoVentaForm $form;

    public function save() {
        $this->form->store();

        session()->flash('message', 'Punto de venta creado con exito.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.puntosventa.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
