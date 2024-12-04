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

    public function loadGpsDataUnicos(){
        //Ultimo de cada unidad
        $this->gpsPoints = DB::table('gps')
            ->select('id', 'lat', 'lng', 'unidads_id')
            ->whereNull('deleted_at')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                        ->from('gps')
                        ->whereNull('deleted_at')
                        ->groupBy('unidads_id');
            })
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'lat' => (float)$point->lat,
                    'lng' => (float)$point->lng,    
                    'unidads_id' => $point->unidads_id,
                    'title' => "Movil #" . ($point->id),
                    'label' => "Movil #" . ($point->unidads_id) . "<br>Patente " . ($point->patente ?? 'N/A')               
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}