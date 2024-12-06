<div>
    <!-- PAGE-HEADER -->

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->


    <div class="row">
        <div class="form-group col-md-3">
            <label for="model">Unidad</label>
            <select id="model" wire:model="current.unidads" class="form-control">
                @if($unidads != null && $unidads)
                <option value="">Seleccione una unidad</option>
                    @foreach($unidads as $unidad)
                        <option value="{{$unidad->id}}">{{$unidad->unidad}}</option>
                    @endforeach
                @else
                    <option value="" disabled selected>Sin unidades disponibles</option>
                @endif
            </select>
        </div>
        @php
            $now = now()->setTimezone('America/Argentina/Buenos_Aires');
            $minDate = $now->copy()->subDays(30); // 30 días antes de la fecha actual
        @endphp

        <div class="form-group col-md-3">
            <label for="fechainicio">Fecha inicio:</label>
            <input 
                class="form-control" 
                min="{{ $minDate->format('Y-m-d\TH:i') }}" 
                max="{{ $now->format('Y-m-d\TH:i') }}" 
                wire:model="current.fechainicio" 
                type="datetime-local" 
                name="fechainicio" 
                id="fechainicio">
        </div>

        <div class="form-group col-md-3">
            <label for="fechafin">Fecha fin:</label>
            <input 
                class="form-control" 
                min="{{ $minDate->format('Y-m-d\TH:i') }}" 
                max="{{ $now->format('Y-m-d\TH:i') }}" 
                value="{{ $now->format('Y-m-d\TH:i') }}" 
                wire:model="current.fechafin" 
                type="datetime-local" 
                name="fechafin" 
                id="fechafin">
        </div>

    </div>
    <div wire:ignore>
        <div id="map" style="height: 600px; width: 100%;"></div>
        <!-- <div id="panel"></div> -->
    </div>

</div>
@script    
<script>          
    let unidads = null;
    window.addEventListener('DOMContentLoaded', function () {    
        setInterval(function () {                
            if(!unidads){                
                $wire.actualizarGpsPoints();            
            }        
        }, 30000);        
    });
    const toggleSidebar = document.querySelector('#sidebar-toggle');
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    let map, markers = [];    
    let points = @json($gpsPoints);        
    const center = { lat: -27.47975561390458, lng: -58.81530992766529 };

    toggleSidebar.click();    

    map = new google.maps.Map(document.getElementById('map'), {
            center,
            zoom: 14,
            mapId: "DEMO_MAP_ID",
        });
    $wire.on('current', (data) => {         
        unidads = data[0].data.unidads;              
                   
    });

    $wire.on('pointsUpdated', (data) => { 
        points = data[0].data;                        
        initMaps();
    });

    document.getElementById('model').addEventListener('change', function(){        
        $wire.setUnidad();
    });

    document.getElementById('fechainicio').addEventListener('change', function(){        
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechafin');
        if( fechaFin.value < fechaInicio.value && fechaFin.value != '' && fechaInicio.value != ''){
            alert('La fecha de inicio debe ser menor a la fecha de fin');
            fechaInicio.value = '';
            fechaFin.value = '';
        }else{
            $wire.actualizarFecha();
        }
         
    });

    document.getElementById('fechafin').addEventListener('change', function(){
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechafin');
        if( fechaFin.value < fechaInicio.value && fechaFin.value != '' && fechaInicio.value != ''){
            alert('La fecha de inicio debe ser menor a la fecha de fin');
            fechaInicio.value = '';
            fechaFin.value = '';
        }else{
            $wire.actualizarFecha();
        }
    })

    function initMaps() {        
        
        markers.forEach(marker => marker.map = null);
        markers = [];

        
        if (window.renderers) {
            window.renderers.forEach(renderer => renderer.setMap(null)); 
        }
        window.renderers = []; 

        
        Object.keys(points).forEach(unitId => {
            const unitPoints = points[unitId];

            if (unitPoints.length > 1) {
                
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: "#000000",
                        strokeOpacity: 1,
                        strokeWeight: 3
                    }
                });
                directionsRenderer.setMap(map);
                window.renderers.push(directionsRenderer); 

                
                directionsRenderer.setDirections({ routes: [] });

                const directionsService = new google.maps.DirectionsService();
                const routeBatches = splitRouteIntoBatches(unitPoints);

                routeBatches.forEach((batchPoints, batchIndex) => {
                    const waypoints = batchPoints.slice(1, -1).map(point => ({
                        location: { lat: point.lat, lng: point.lng },
                        stopover: true
                    }));
                    const request = {
                        origin: { lat: batchPoints[0].lat, lng: batchPoints[0].lng },
                        destination: { 
                            lat: batchPoints[batchPoints.length - 1].lat, 
                            lng: batchPoints[batchPoints.length - 1].lng 
                        },
                        waypoints: waypoints,
                        optimizeWaypoints: false,
                        travelMode: google.maps.TravelMode.DRIVING
                    };

                    directionsService.route(request, (result, status) => {
                        if (status === 'OK') {
                            if (batchIndex === 0) {
                                directionsRenderer.setDirections(result);
                            } else {
                                const currentRoute = directionsRenderer.getDirections();
                                const newRoutes = result.routes[0];

                                currentRoute.routes[0].legs.push(...newRoutes.legs);
                                currentRoute.routes[0].overview_path.push(...newRoutes.overview_path);

                                directionsRenderer.setDirections(currentRoute);
                            }
                        } else {
                            console.error(`Error al obtener la ruta del lote ${batchIndex}:`, status);
                        }
                    });

                    if (batchIndex === routeBatches.length - 1) {                            
                        añadirMarcador(batchPoints[batchPoints.length - 1]);
                    }
                });

            } else {
                
                unitPoints.forEach((point, index) => {                        
                                            
                    const property = {
                        unidads_id: point.unidads_id || 'N/A',
                        unidads_unidad: point.unidads_unidad || 'N/AA', 
                        title: point.title,
                        pathImg: "{{ asset('assets/images/thumbnails/img48-32.png') }}"
                    };
                    
                    const content = buildContent(property);                                                
                    
                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        id: point.id,
                        position: point, 
                        map: map,
                        content: content, 
                        title: `${index + 1}. ${point.title}`, 
                        gmpClickable: true,
                    });
                    
                    marker.addListener("click", () => {                            
                        toggleHighlight(marker, property);
                    });
                    
                    markers.push(marker);
                });
            }
        });
    }

    function splitRouteIntoBatches(unitPoints, maxWaypoints = 25) {
        const batches = [];
        const maxPointsPerBatch = maxWaypoints - 1;

        
        if (unitPoints.length <= maxWaypoints) {
            return [unitPoints];
        }

        
        for (let i = 0; i < unitPoints.length; i += maxPointsPerBatch) {
            const batchPoints = unitPoints.slice(i, i + maxPointsPerBatch + 1);
            batches.push(batchPoints);
        }

        return batches;
    }

    function añadirMarcador(point){                            
        const property = {
            unidads_id: point.unidads_id || 'N/A',
            unidads_unidad: point.unidads_unidad || 'N/AA', 
            title: point.title,
            pathImg: "{{ asset('assets/images/thumbnails/img48-32.png') }}"
        };
        
        const content = buildContent(property);

        const marker = new google.maps.marker.AdvancedMarkerElement({
            id: point.id,
            position: point,
            map: map,
            content: content,
            title: `${point.title}`,
            gmpClickable: true,
        });
        markers = [];
        markers.push(marker);
        marker.addListener("click", () => {
            toggleHighlight(marker, point);
        });                            
    }

    function buildContent(property) {
        const content = document.createElement("div");

        content.classList.add("property");
        content.innerHTML = `
            <div class="icon">
                <img src="${property.pathImg}" alt="Image for ${property.title}" style="width: 48px; height: 32px;">
            </div>
            <div class="details">
                <div class="price">Unidad: ${property.unidads_id}</div>
                <div class="address">Detalle: ${property.unidads_unidad}</div>
                
            </div>
            `;
        return content;
    }
  
    function toggleHighlight(markerView, property) {
        if (markerView.content.classList.contains("highlight")) {
            markerView.content.classList.remove("highlight");
            markerView.zIndex = null;
        } else {
            markerView.content.classList.add("highlight");
            markerView.zIndex = 1;
        }
    }


    initMaps();
</script>
@endscript


