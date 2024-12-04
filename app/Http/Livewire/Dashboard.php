<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $gpsPoints = [];

    public function mount()
    {
        $this->loadGpsData();
    }

    public function loadGpsData()
    {
        // Consulta para obtener los datos de la tabla GPS
        $this->gpsPoints = DB::table('gps')
            ->select('id', 'lat', 'lng', 'unidads_id')
            ->whereNull('deleted_at') // Suponiendo que quieres solo los activos
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'lat' => (float)$point->lat,
                    'lng' => (float)$point->lng,    
                    'unidads_id' => $point->unidads_id,
                    'title' => "Movil #" . ($point->unidads_id),
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