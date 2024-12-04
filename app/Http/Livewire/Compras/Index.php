<?php

namespace App\Http\Livewire\Compras;

use App\Models\Compra;
use Livewire\Component;
use NumberFormatter;

class Index extends Component
{

    public $title = "Listado de compras";

    public function formatCurrency($amount)
    {
        $formatter = new NumberFormatter('es_AR', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, 'ARS');
    }

    public function render()
    {

        $compras = Compra::orderBy("id", "desc")->with("contacto")->paginate(10);

        foreach ($compras as $compra) {
            $compra->importe_subtotal = $this->formatCurrency($compra->importe_subtotal);
            $compra->importe_iva = $this->formatCurrency($compra->importe_iva);
            $compra->importe_descuento = $this->formatCurrency($compra->importe_descuento);
            $compra->importe_total = $this->formatCurrency($compra->importe_total);
        }

        return view('livewire.compras.index', ['compras' => $compras]);
    }
}
