<?php

namespace App\Http\Livewire\Compras;

use App\Models\CompraDetalle;
use Livewire\Component;
use NumberFormatter;

class Detalle extends Component
{
    public $title = "Detalle de compra";

    public $detalles = [];

    public function mount($id)
    {
        $this->detalles = CompraDetalle::where("compras_id", $id)
            ->with("producto")
            ->with("deposito")
            ->get();

        foreach ($this->detalles as $detalle) {
            $detalle->importe_total = $this->formatCurrency($detalle->importe_total);
            $detalle->producto->precio = $this->formatCurrency($detalle->producto->precio);
        }
    }

    public function formatCurrency($amount)
    {
        if ($amount) {
            $formatter = new NumberFormatter('es_AR', NumberFormatter::CURRENCY);
            return $formatter->formatCurrency(floatval($amount), 'ARS');
        }
    }

    public function render()
    {
        return view('livewire.compras.detalle');
    }
}
