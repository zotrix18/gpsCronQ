<?php

namespace App\Http\Livewire\Puntosventa;

use Livewire\Component;

use App\Livewire\Forms\PuntoVentaForm;

use App\Models\PuntoVenta;

class Update extends Component
{
    public $title = 'Actualizar Punto de venta';
    public $subtitle = 'Actualización';
    public PuntoVentaForm $form;

    public function mount($id)
    {
        $puntoventa = PuntoVenta::findOrFail($id);
        $this->form->setPuntoVenta($puntoventa);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Punto de venta actualizado con éxito.');
    }

    public function render()
    {
        return view('livewire.puntosventa.create', [
            'title' => $this->title,
            'subtitle' => $this->subtitle
        ]);
    }
}
