<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

use App\Models\PuntoVenta;

class PuntoVentaForm extends Form
{
    public ?PuntoVenta $pv;

    #[Validate('required|max:125')]
    public $puntosventa = '';

    #[Validate('required|numeric|max:99999999999')]
    public $afip_id = '';

    public function setPuntoVenta(PuntoVenta $puntoventa) {
        $this->pv = $puntoventa;

        $this->puntosventa = $puntoventa->puntosventa;
        $this->afip_id = $puntoventa->afip_id;
    }

    public function store() {
        $this->validate();

        $puntoventa = new PuntoVenta();
        $puntoventa->puntosventa = $this->puntosventa;
        $puntoventa->afip_id = $this->afip_id;
        $puntoventa->empresas_id = session('empresa')->id;
        $puntoventa->activo = 1;
        
        $puntoventa->save();
    }

    public function update() {
        $this->validate();

        $puntoventa = PuntoVenta::find($this->pv->id);
        $puntoventa->puntosventa = $this->puntosventa;
        $puntoventa->afip_id = $this->afip_id;

        $puntoventa->update();
    }
}
