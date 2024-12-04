<?php

namespace App\Http\Livewire\Ventas;

use NumberFormatter;
use Livewire\Component;
use Livewire\WithPagination;

use Carbon\Carbon;

use App\Models\Venta;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public $title = "Listado de ventas";
    public $querySearch = '';

    public function formatCurrency($amount)
    {
        $formatter = new NumberFormatter('es_AR', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, 'ARS');
    }

    public function render()
    {
        $ventas = Venta::whereHas('puntoVenta', function ($query) {
                            $query->where('empresas_id', session('empresa')->id);
                        })
                        ->where('numero', 'like', '%' . $this->querySearch)
                        ->orderBy("id", "desc")
                        ->paginate(10);

        foreach ($ventas as $venta) {
            $venta->importe_apagar = $this->formatCurrency($venta->importe_apagar);
            $venta->importe_total = $this->formatCurrency($venta->importe_total);

            $venta->fecha = Carbon::parse($venta->fecha)->format('d/m/Y H:i:s');
            $venta->fecha_vencimiento = Carbon::parse($venta->fecha_vencimiento)->format('d/m/Y');
        }

        return view('livewire.ventas.index', [
            'title' => $this->title,
            'ventas' => $ventas
        ]);
    }
}
