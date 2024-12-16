<div>
    <!-- PAGE-HEADER -->

    <div class="page-header">
        <div>
            <h1 class="page-title">MAPA</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mapa</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->


    <div class="row">
        <div class="form-group col-md-2">
            <label for="model">Unidad</label>
            <select id="model" wire:model="current.unidads" class="form-control">
                @if($unidads != null && $unidads)
                <option id="unidad" value="">Seleccione una unidad</option>
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

        <div class="form-group col-md-2">
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

        <div class="form-group col-md-2">
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
                console.log('actualizar');
                
                $wire.actualizarGpsPoints();
            }
        }, 5000);
    });
    const toggleSidebar = document.querySelector('#sidebar-toggle');
    const imgEstatica = "{{ asset('assets/images/cars/redCar48-24.png') }}";
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    let map, markers = [];
    let points = @json($gpsPoints);
    const center = { lat: -27.47975561390458, lng: -58.81530992766529 };
    const activeMarkers = new Map();

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
        // console.log(points);
        
        initMaps();
    });

    document.getElementById('model').addEventListener('change', function(){        
        $wire.setUnidad();
    });

    document.getElementById('fechainicio').addEventListener('change', function(){        
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechafin');
        const unidad = document.getElementById('unidad');

        if( fechaFin.value < fechaInicio.value && fechaFin.value != '' && fechaInicio.value != ''){
            alert('La fecha de inicio debe ser menor a la fecha de fin');
            // fechaInicio.value = '';
            // fechaFin.value = '';
        }else{
            $wire.actualizarFecha();
        }
         
    });

    document.getElementById('fechafin').addEventListener('change', function(){
        const fechaInicio = document.getElementById('fechainicio');
        const fechaFin = document.getElementById('fechafin');
        if( fechaFin.value < fechaInicio.value && fechaFin.value != '' && fechaInicio.value != ''){
            alert('La fecha de inicio debe ser menor a la fecha de fin');
            // fechaInicio.value = '';
            // fechaFin.value = '';
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

        activeMarkers.forEach(marker => marker.map = null);
        activeMarkers.clear();

        Object.keys(points).forEach(unitId => {
            const unitPoints = points[unitId];

            const directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: false,
                polylineOptions: {
                    strokeColor: "#000000",
                    strokeOpacity: 1,
                    strokeWeight: 3
                }
            });
            if (unitPoints.length > 1) {
                directionsRenderer.setMap(map);
                window.renderers.push(directionsRenderer); 

                directionsRenderer.setDirections({ routes: [] });

                const directionsService = new google.maps.DirectionsService();
                const routeBatches = splitRouteIntoBatches(unitPoints);
                const totalBatches = routeBatches.length;
                let processedBatches = 0;
                let completeRoute = [];

                routeBatches.forEach((batchPoints, batchIndex) => {
                    console.log(batchPoints);
                    
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
                            // Acumular rutas
                            if (batchIndex === 0) {
                                directionsRenderer.setDirections(result);
                                completeRoute = result.routes[0].overview_path;
                            } else {
                                const currentRoute = directionsRenderer.getDirections();
                                const newRoutes = result.routes[0];
                                

                                currentRoute.routes[0]?.legs.push(...newRoutes?.legs);
                                currentRoute.routes[0]?.overview_path.push(...newRoutes.overview_path);
                                completeRoute.push(...newRoutes.overview_path);

                                directionsRenderer.setDirections(currentRoute);
                            }

                            processedBatches++;

                            // Cuando se procesen todos los lotes, animar el marcador
                            if (processedBatches === totalBatches) {
                                animateMarkerAlongRoute(completeRoute, unitId, batchPoints);
                            }
                        } else {
                            console.error(`Error al obtener la ruta del lote ${batchIndex}:`, status);
                        }
                    });
                });
            } else {
                // Manejo de puntos únicos (sin ruta)
                unitPoints.forEach((point, index) => {
                    directionsRenderer.setDirections({ routes: [] });
                    const property = {
                        unidads_id: point.unidads_id || 'N/A',
                        unidads_unidad: point.unidads_unidad || 'N/AA', 
                        title: point.title,
                        pathImg: imgEstatica
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
    
    function animateMarkerAlongRoute(routePath, unitId, point) {        
        if (activeMarkers.has(unitId)) {
            const existingMarker = activeMarkers.get(unitId);
            existingMarker.map = null;
            activeMarkers.delete(unitId);
        }

        const property = {
            unidads_id: point[0].unidads_id || 'N/A',
            unidads_unidad: point[0].unidads_unidad || 'N/A',
            title: point[0].title,
            pathImg: point[0].pathImg || imgEstatica
        };

        let content;

        if (property.pathImg) {
            content = buildContent(property);
        } else {
            const markerContent = createMarkerContent(unitId);

            const detailsContainer = document.createElement('div');
            detailsContainer.style.position = 'absolute';
            detailsContainer.style.bottom = '-40px';
            detailsContainer.style.width = '100px';
            detailsContainer.style.textAlign = 'center';
            detailsContainer.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
            detailsContainer.style.borderRadius = '4px';
            detailsContainer.style.padding = '5px';
            detailsContainer.style.fontSize = '12px';
            detailsContainer.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';

            detailsContainer.innerHTML = `
                <div><strong>Unidad:</strong> ${property.unidads_id}</div>
                <div><strong>Detalle:</strong> ${property.unidads_unidad}</div>
            `;

            const combinedContent = document.createElement('div');
            combinedContent.appendChild(markerContent);
            combinedContent.appendChild(detailsContainer);

            content = combinedContent;
        }

        // Selecciona el elemento que se rotará
        const rotatableElement = content.querySelector('.rotatable-point') || content.querySelector('img');
        if (rotatableElement) {
            rotatableElement.style.transition = 'transform 0.1s linear';
        }

        const animationMarker = new google.maps.marker.AdvancedMarkerElement({
            map: map,
            content: content,
            position: routePath[0]
        });

        activeMarkers.set(unitId, animationMarker);

        let currentIndex = 0;
        let progress = 0;
        const ANIMATION_DURATION = 10;
        const INTERPOLATION_STEPS = 30;

        function calculateRotationAngle(start, end) {
            const startLatLng = new google.maps.LatLng(start);
            const endLatLng = new google.maps.LatLng(end);

            const deltaLat = endLatLng.lat() - startLatLng.lat();
            const deltaLng = endLatLng.lng() - startLatLng.lng();

            if (Math.abs(deltaLat) > Math.abs(deltaLng)) {
                // Movimiento principalmente vertical
                return deltaLat > 0 ? -90 : -270; // Hacia arriba o hacia abajo
            } else {
                // Movimiento principalmente horizontal
                return deltaLng > 0 ? 0 : 180; // Hacia la derecha o hacia la izquierda
            }
        }

        function smoothInterpolation(start, end, t) {
            const easeInOut = t => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            return {
                lat: start.lat() + (end.lat() - start.lat()) * easeInOut(t),
                lng: start.lng() + (end.lng() - start.lng()) * easeInOut(t)
            };
        }

        function animateStep() {
            if (currentIndex >= routePath.length - 1) {
                animationMarker.map = null;
                activeMarkers.delete(unitId);
                añadirMarcador(point[point.length - 1]);
                return;
            }

            progress += 1 / INTERPOLATION_STEPS;

            if (progress >= 1) {
                currentIndex++;
                progress = 0;
            }

            if (currentIndex < routePath.length - 1) {
                const startPoint = new google.maps.LatLng(routePath[currentIndex]);
                const endPoint = new google.maps.LatLng(routePath[currentIndex + 1]);

                const interpolatedPosition = smoothInterpolation(startPoint, endPoint, progress);
                animationMarker.position = interpolatedPosition;

                // Calcular y aplicar rotación
                if (rotatableElement) {
                    const rotationAngle = calculateRotationAngle(routePath[currentIndex], routePath[currentIndex + 1]);
                    // console.log(rotationAngle);
                    
                    switch (rotationAngle) {
                        case -90:
                            rotatableElement.style.transform = 'rotate(-90deg) scaleX(1)';
                            break;
                        case 90:
                            rotatableElement.style.transform = 'rotate(90deg) scaleX(1)';
                            break;
                        case 180:
                            rotatableElement.style.transform = 'rotate(180deg) scaleY(-1)';
                            break;
                        default:
                            rotatableElement.style.transform = `rotate(${rotationAngle}deg) scaleX(1)`;
                    }
                    // if (rotationAngle === 0) {
                    //     // Hacia la derecha, espejado
                    //     rotatableElement.style.transform = `rotate(${rotationAngle}deg) scaleX(1)`;
                    // } else {
                    //     // Rotación normal para otras direcciones
                    //     rotatableElement.style.transform = `rotate(${rotationAngle}deg) scaleX(1)`;
                    // }
                }
            }

            setTimeout(animateStep, ANIMATION_DURATION);
        }

        animateStep();
    }


    function createMarkerContent(unitId) {
        // Personalizar el contenido del marcador
        const markerDiv = document.createElement('div');
        markerDiv.style.position = 'relative';
        markerDiv.style.width = '40px';
        markerDiv.style.height = '40px';

        // Crear un contenedor para la animación
        const markerContainer = document.createElement('div');
        markerContainer.style.width = '100%';
        markerContainer.style.height = '100%';
        markerContainer.style.position = 'absolute';
        markerContainer.style.borderRadius = '50%';
        markerContainer.style.background = 'rgba(0, 123, 255, 0.5)';
        markerContainer.style.animation = 'pulse 1.5s infinite';

        // Añadir estilos de animación
        const styleSheet = document.createElement('style');
        styleSheet.textContent = `
            @keyframes pulse {
                0% {
                    transform: scale(0.8);
                    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
                }
                70% {
                    transform: scale(1.2);
                    box-shadow: 0 0 0 20px rgba(0, 123, 255, 0);
                }
                100% {
                    transform: scale(0.8);
                    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
                }
            }
        `;
        document.head.appendChild(styleSheet);

        // Icono central
        const icon = document.createElement('div');
        icon.style.position = 'absolute';
        icon.style.top = '50%';
        icon.style.left = '50%';
        icon.style.transform = 'translate(-50%, -50%)';
        icon.style.width = '20px';
        icon.style.height = '20px';
        icon.style.borderRadius = '50%';
        icon.style.background = 'blue';
        icon.style.zIndex = '10';

        markerDiv.appendChild(markerContainer);
        markerDiv.appendChild(icon);

        return markerDiv;
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

    function splitRouteIntoBatches(unitPoints, maxWaypoints = 23) {
        const batches = [];

        if (unitPoints.length <= maxWaypoints) {
            return [unitPoints];
        }
       
        for (let i = 0; i < unitPoints.length; i += (maxWaypoints - 1)) {
            const batchPoints = unitPoints.slice(
                i, 
                Math.min(i + maxWaypoints + 1, unitPoints.length)
            );
            batches.push(batchPoints);
        }

        return batches;
    }
    
    function añadirMarcador(point){
        const property = {
            unidads_id: point.unidads_id || 'N/A',
            unidads_unidad: point.unidads_unidad || 'N/AA', 
            title: point.title,
            pathImg: imgEstatica
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
    
    initMaps();
</script>
@endscript


