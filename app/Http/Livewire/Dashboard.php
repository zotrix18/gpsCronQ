<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $gpsPoints = [];

    public function mount()
    {
        $this->loadGpsDataUnicos();
    }

    public function loadGpsDataUnicos($objFiltros = [])
        {
            // $objFiltros['unidads_id'] = 1;
            $query = DB::table('gps')
                ->select('id', 'lat', 'lng', 'unidads_id', 'created_at')
                ->whereNull('deleted_at');
        
            if (isset($objFiltros['unidads_id'])) {
                // Caso 1: unidads_id especificado -> Obtener últimos 100 registros de esa unidad
                $query->whereIn('id', function($query) use ($objFiltros) { // Aquí pasamos $objFiltros como 'use'
                    $query->select('id')
                        ->from(function($subquery) use ($objFiltros) { // Usamos 'use' para acceder a $objFiltros
                            $subquery->select(
                                    'id', 
                                    'unidads_id', 
                                    'created_at', 
                                    'lat', 
                                    'lng',
                                    DB::raw('ROW_NUMBER() OVER (PARTITION BY unidads_id, lat, lng ORDER BY id DESC) as rn')
                                )
                                ->from('gps')
                                ->whereNull('deleted_at')
                                ->where('unidads_id', $objFiltros['unidads_id']); // Aquí accedemos correctamente a $objFiltros
                        }, 'ranked_gps')
                        ->where(function($subquery) {
                            // Solo tomar el registro más reciente de cada ubicación exacta
                            $subquery->where('rn', '=', 1);
                        });
                });
            } else {
                // Caso 2: unidads_id no especificado -> Obtener el último registro de cada unidad
                $query->whereIn('id', function ($subQuery) {
                    $subQuery->select('id')
                        ->from(function ($rankedQuery) {
                            $rankedQuery->select(
                                'id',
                                'unidads_id',
                                'created_at',
                                'lat',
                                'lng',
                                DB::raw('ROW_NUMBER() OVER (PARTITION BY unidads_id ORDER BY created_at DESC) as rn')
                            )
                            ->from('gps')
                            ->whereNull('deleted_at');
                        }, 'ranked_gps')
                        ->where('rn', '=', 1); // Tomar el registro más reciente por unidad
                });
            }
        
            // Ejecutar la consulta
            $gpsPointsRaw = $query->get();
        
            // Transformar los datos en la estructura deseada
            $this->gpsPoints = $gpsPointsRaw->groupBy('unidads_id')
                ->map(function ($points) {
                    return $points->map(function ($point) {
                        return [
                            'id' => $point->id,
                            'lat' => (float)$point->lat,
                            'lng' => (float)$point->lng,
                            'title' => "Movil #" . ($point->id),
                            'label' => "Dispositivo #" . ($point->unidads_id) . "<br>Último reg " . ($point->id ?? 'N/A'),
                        ];
                    })->values()->toArray();
                })
                ->toArray();
        }

    public function render()
    {
        return view('livewire.dashboard');
    }
}