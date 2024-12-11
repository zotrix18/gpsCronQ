<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $gpsPoints = [];
    public $empresa = null;
    public $unidads = null;
    public $current = [
        'unidads' => null,
        'fechainicio' => null,
        'fechafin' => null,
        // 'fechainicio' => '2024-12-09 10:50:00',
        // 'fechafin' => '2024-12-09 10:55:00',
    ];

    public function mount()
    {
        $this->empresa = session('empresa');
        $this->loadGpsDataUnicos();
        $this->loadUnidads();
    }
    
    public function loadGpsDataUnicos($objFiltros = []){
        $empresa = session('empresa');
        $query = DB::table('gps')
        ->select(
            'gps.id',
            'gps.lat',
            'gps.lng',
            'gps.unidads_id',
            'gps.created_at',
            'unidads.unidad',
            'unidads.codigo',
            'unidads.activo'
        )
        ->join('unidads', 'gps.unidads_id', '=', 'unidads.id')
        ->where('gps.lat', '!=', 0)
        ->where('gps.lng', '!=', 0)
        ->where('unidads.empresas_id', $empresa->id)
        ->whereNull('gps.deleted_at'); 
    
    if ($this->current['unidads'] != null && $this->current['fechainicio'] != null && $this->current['fechafin'] != null) {        
        $fechainicio = Carbon::parse($this->current['fechainicio'])->format('Y-m-d H:i:s');
        $fechafin = Carbon::parse($this->current['fechafin'])->format('Y-m-d H:i:s');                
        $query->whereBetween('gps.created_at', [$fechainicio, $fechafin]);
    }
    
    if ($this->current['unidads']) {        
        $query->whereIn('gps.id', function ($subQuery) use ($objFiltros) {
            $subQuery->select('id')
                ->from(function ($subquery) use ($objFiltros) {
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
                    ->where('unidads_id', $objFiltros['unidads']);
                }, 'ranked_gps')
                ->where('rn', '<=', 100);
        });
    } else {
        
        $query->whereIn('gps.id', function ($subQuery) {
            $subQuery->select(DB::raw('MAX(id)'))
                ->from('gps')
                ->whereNull('deleted_at')
                ->groupBy('unidads_id');
        });
    }
    
    $gpsPointsRaw = $query->get();
    
        
        $this->gpsPoints = $gpsPointsRaw->groupBy('unidads_id')
            ->map(function ($points) {
                return $points->map(function ($point) {
                    return [
                        'id' => $point->id,
                        'lat' => (float) $point->lat,
                        'lng' => (float) $point->lng,
                        'unidads_unidad'=>$point->unidad,
                        'unidads_id'=>$point->unidads_id,
                        'unidads_codigo'=>$point->codigo,
                        'unidads_activo'=>$point->activo,                        
                        'title' => "Unidad: " . ($point->id ?? 'N/A'),
                        'label' => "Dispositivo: #" . ($point->codigo ?? 'N/A') . "<br>Activo: " . ($point->activo ? 'Sí' : 'No'),
                    ];
                })->values()->toArray();
            })
            ->toArray();
    }
    
    private function loadUnidads(){
        $this->unidads = DB::table('unidads')->where('empresas_id', $this->empresa->id)->whereNull('deleted_at')->get();
        $this->dispatch('current', ['data'=> $this->current]);
    }

    public function actualizarGpsPoints(){
        $this->loadGpsDataUnicos($this->current);
        $this->dispatch('pointsUpdated', ['data'=> $this->gpsPoints]);
    }

    public function setUnidad (){        
        $this->loadGpsDataUnicos($this->current);
        $this->dispatch('pointsUpdated', ['data'=> $this->gpsPoints]);
        $this->dispatch('current', ['data'=> $this->current]);
    }

    public function actualizarFecha(){
        $this->loadGpsDataUnicos($this->current);
        $this->dispatch('pointsUpdated', ['data'=> $this->gpsPoints]);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}