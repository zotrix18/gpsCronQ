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
        'fechainicio' => '2024-12-04 20:55:00',
        'fechafin' => '2024-12-04 20:59:00',
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
            'gps.lat',
            'gps.lng',
            DB::raw('MIN(gps.id) as id'),
            DB::raw('MIN(gps.unidads_id) as unidads_id'),
            DB::raw('MIN(gps.created_at) as created_at'),
            DB::raw('MIN(unidads.unidad) as unidad'),
            DB::raw('MIN(unidads.codigo) as codigo'),
            DB::raw('MIN(unidads.activo) as activo'),
            DB::raw('MIN(unidads.path) as path'),
        )
        ->join('unidads', 'gps.unidads_id', '=', 'unidads.id')
        ->where('gps.lat', '!=', 0)
        ->where('gps.lng', '!=', 0)
        ->where('unidads.empresas_id', $empresa->id)
        ->whereNull('gps.deleted_at')
        ->groupBy('gps.lat', 'gps.lng'); // Agrupa por latitud y longitud
    
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
                    ->where('rn', '<=', 2);
            });
        } else {
            $query->whereIn('gps.id', function ($subQuery) {
                $subQuery->select(DB::raw('MAX(id)'))
                    ->from('gps')
                    ->whereNull('deleted_at')
                    ->groupBy('unidads_id');
            });
        }
        
        $gpsPointsRaw = $query->orderBy('gps.id', 'asc')->get();
    
        if ($this->current['unidads']) {
            $cleanedGpsPoints = $this->filterNoiseData($gpsPointsRaw);
            $this->gpsPoints = $cleanedGpsPoints->groupBy('unidads_id');
        }else{
            // $this->gpsPoints = $this->añadirExtraDatos($gpsPointsRaw);
            $this->gpsPoints = $gpsPointsRaw->groupBy('unidads_id');
        }
        $this->gpsPoints = $this->gpsPoints
            ->map(function ($points) {
                return $points->map(function ($point) {
                    return [
                        'id' => $point->id,
                        'lat' => (float) $point->lat,
                        'lng' => (float) $point->lng,
                        'unidads_unidad' => $point->unidad,
                        'unidads_id' => $point->unidads_id,
                        'unidads_codigo' => $point->codigo,
                        'unidads_activo' => $point->activo,
                        'avgSpd' => $point->avgSpd?? null,
                        'speed' => $point->speed?? null,
                        'title' => "Unidad: " . ($point->id ?? 'N/A'),
                        'label' => "Dispositivo: #" . ($point->codigo ?? 'N/A') . "<br>Activo: " . ($point->activo ? 'Sí' : 'No'),
                    ];
                })->values()->toArray();
            })
            ->toArray();
    }

    // private function añadirExtraDatos($gpsPoints) {        
        //     $distance = $this->calculateDistance(
        //         $gpsPoints[0]->lat, $gpsPoints[0]->lng, 
        //         $gpsPoints[1]->lat, $gpsPoints[1]->lng
        //     );
        //     $timeDiff = abs(Carbon::parse($gpsPoints[1]->created_at)
        //     ->diffInSeconds(Carbon::parse($gpsPoints[0]->created_at)));

        //     $gpsPoints[1]->speed = ($distance / $timeDiff) * 3.6;
        
    // }
    
    private function filterNoiseData($gpsPoints) {
        $cleanedPoints = collect();
        $lastValidPoints = [];
        $promedio = [
            'tiempo'=> 0,
            'cantTiempo' => 0,
            'distancia'=> 0,
            'cantDistancia' => 0,
        ];
        // $distanciaTotal = 0;
        // $cantidadDistancia = 0;
    
        foreach ($gpsPoints as $point) {
            if (!isset($lastValidPoints[$point->unidads_id])) {
                $lastValidPoints[$point->unidads_id] = $point;
                $cleanedPoints->push($point);
                continue;
            }
    
            $lastPoint = $lastValidPoints[$point->unidads_id];
            
            // Calcular distancia
            $distance = $this->calculateDistance(
                $lastPoint->lat, $lastPoint->lng, 
                $point->lat, $point->lng
            );
            $promedio['distancia'] += $distance;
            $promedio['cantDistancia']++;
    
            // Calcular de tiempo entre puntos
            $timeDiff = abs(Carbon::parse($point->created_at)
            ->diffInSeconds(Carbon::parse($lastPoint->created_at)));
            $promedio['tiempo'] += $timeDiff;
            $promedio['cantTiempo']++;
                
            $maxSpeed = 150; // Velocidad máxima permitida
            $speed = ($distance / $timeDiff) * 3.6; //km/h
    
            // Filtros para eliminar datos ruidosos:
            // 1. Velocidad máxima
            // 2. Distancia mínima entre puntos
            // 3. Tiempo mínimo entre puntos
            $promedioDistancia = $promedio['distancia'] / $promedio['cantDistancia']; //Promedio de distancia entre puntos
            $promedioTiempo = $promedio['tiempo'] / $promedio['cantTiempo']; //Promedio de tiempo entre puntos
            if (
                $speed <= $maxSpeed && 
                $distance > $promedioDistancia && // Distancia minimia entre puntos
                $timeDiff > 14 // segundos minimos entre puntos
            ) {
                $lastValidPoints[$point->unidads_id] = $point;
                $point->avgSpd = $promedioDistancia / $promedioTiempo;
                $point->speed = $speed;
                $cleanedPoints->push($point);
            }
        }
        // $velProm = $promedioDistancia/$promedioTiempo;
        $cleanedPoints[0]->avgSpd = $promedioDistancia / $promedioTiempo;
        return $cleanedPoints;
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        //Calculo de la distancia usando la formula de haversine
        //Esta permite obtener la distancia en metros de dos puntos
        $earth_radius = 6371000; // Radio de la Tierra en metros
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
        return $earth_radius * $c; // Distancia en metros
    }
    
    private function loadUnidads(){
        $this->unidads = DB::table('unidads')->where('empresas_id', $this->empresa->id)->where('activo', true)->whereNull('deleted_at')->get();
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