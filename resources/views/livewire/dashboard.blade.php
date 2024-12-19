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
        <div class="form-group" style="max-width: 60px;margin-left: auto;">
            <select id="update-speed" class="form-control">                
                    <option value="5">5s</option>
                    <option value="10">10s</option>
                    <option value="15" selected>15s</option>
                    <option value="600">600s</option>
            </select>
        </div>
        <!-- <div id="panel"></div> -->
    </div>

</div>
@script
<script>
    let unidads = null;
    const updateSpeedSelect = document.getElementById('update-speed');
    window.addEventListener('DOMContentLoaded', function () {    
        let intervalId = null;
        const updateInterval = (speed) => {
            if(intervalId){
                clearInterval(intervalId);
            }
            intervalId = setInterval(function () {
                if(!unidads){
                    console.log('actualizar');
                    
                    $wire.actualizarGpsPoints();
                }
            }, speed * 1000);
        }
        updateSpeedSelect.addEventListener('change', function(e){
            updateInterval(e.target.value);
        });
        updateInterval(updateSpeedSelect.value);
    });
    const toggleSidebar = document.querySelector('#sidebar-toggle');
    const imgEstatica = "{{ asset('assets/images/cars/redCar.png') }}";
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    let map, markers = [];
    let points = @json($gpsPoints);
    const center = { lat: -27.47975561390458, lng: -58.81530992766529 };
    const activeMarkers = new Map();
    console.log(points);

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
        console.log(points);
        
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

        const speedControlContainer = document.getElementById('speed-control-container');
        if (speedControlContainer) {
            speedControlContainer.remove();
        }

        Object.keys(points).forEach(unitId => {
            const unitPoints = points[unitId];

            const directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
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
                                initMarkerAnimation(completeRoute, unitId, batchPoints);
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
                        obs: point.obs,
                        primaryPath: point.primaryPath,
                        path: point.path ? '{{asset('storage/')}}/' + point.path : point.primaryPath,
                        avgSpd: point.avgSpd,
                        speed: point.speed
                    };
                    
                    const content = buildContent(property);                   
                    const marker = new google.maps.marker.AdvancedMarkerElement({
                        id: point.id,
                        position: point,
                        position: point, 
                        map: map,
                        content: content,
                        title: `${index + 1}. ${point.title}`,
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
    
    function createMarkerAnimator(routePath, unitId, point) {
        let currentIndex = 0;
        let progress = 0;
        let lastRotationAngle = 0;
        
        // Configuración inicial configurable
        let config = {
            interpolationSteps: 100,
            animationDuration: 50,
            speed: 1 // Factor de velocidad por defecto
        };

        const property = {
            unidads_id: point[0].unidads_id || 'N/A',
            unidads_unidad: point[0].unidads_unidad || 'N/A',
            title: point[0].title,
            obs: point[0].obs,
            primaryPath: point[0].primaryPath,
            path: point[0].path ? '{{asset('storage/')}}/' + point[0].path : point[0].primaryPath,
            avgSpd: point[0].avgSpd || null,
            speed: point[0].speed || null,
        };
        
        // Función para crear contenido detallado del marcador
        function createDetailedMarkerContent(property) {
            // Detalles
            const detailsContainer = document.createElement('div');
            detailsContainer.innerHTML = `
                <strong>Unidad:</strong> ${property.unidads_id}<br>
                <strong>Detalle:</strong> ${property.unidads_unidad}<br>
                <strong>Velocidad Promedio:</strong> ${parseFloat(property.avgSpd).toFixed(2)} km/h
            `;

            container.appendChild(closeButton);
            container.appendChild(imgElement);
            container.appendChild(detailsContainer);

            return container;
        }

        const content = buildContent(property);

        const rotatableElement = content.querySelector('.rotatable-point');
        if (rotatableElement) {
            rotatableElement.style.transition = 'transform 0.1s linear';
        }

        const animationMarker = new google.maps.marker.AdvancedMarkerElement({
            map: map,
            content: content,
            position: routePath[0]
        });

        animationMarker.addListener("click", () => {                            
            toggleHighlight(animationMarker, property);
        });

        function setSpeed(newSpeed) {
            config.speed = newSpeed;            
            config.interpolationSteps = Math.max(10, Math.min(200, 100 / newSpeed));
            config.animationDuration = Math.max(10, Math.min(100, 50 / newSpeed));
        }

        function calculateRotationAngle(start, end) {
            const startLatLng = new google.maps.LatLng(start);
            const endLatLng = new google.maps.LatLng(end);

            const deltaLat = endLatLng.lat() - startLatLng.lat();
            const deltaLng = endLatLng.lng() - startLatLng.lng();

            let angle = Math.atan2(deltaLng, deltaLat) * (180 / Math.PI);
            angle -= 85;

            return angle;
        }

        function smoothInterpolation(start, end, t) {
            return {
                lat: start.lat() + (end.lat() - start.lat()) * t,
                lng: start.lng() + (end.lng() - start.lng()) * t
            };
        }

        function animateStep() {
            if (currentIndex >= routePath.length - 1) {
                // Eliminar marcador de animación
                animationMarker.map = null;
                
                // Añadir marcador final con todos los datos y último ángulo
                const finalPoint = point[point.length - 1];
                añadirMarcador(finalPoint, lastRotationAngle);
                
                return;
            }

            progress += config.speed / config.interpolationSteps;

            if (progress >= 1) {
                currentIndex++;
                progress = 0;
            }

            if (currentIndex < routePath.length - 1) {
                const startPoint = new google.maps.LatLng(routePath[currentIndex]);
                const endPoint = new google.maps.LatLng(routePath[currentIndex + 1]);

                const interpolatedPosition = smoothInterpolation(startPoint, endPoint, progress);
                animationMarker.position = interpolatedPosition;

                if (rotatableElement) {
                    lastRotationAngle = calculateRotationAngle(routePath[currentIndex], routePath[currentIndex + 1]);
                    rotatableElement.style.transform = `rotate(${lastRotationAngle}deg)`;
                }
            }

            setTimeout(animateStep, config.animationDuration);
        }
        
        return {
            start: animateStep,
            setSpeed: setSpeed
        };
    }

    function createSpeedControlContainer() {
        const container = document.createElement('div');
        container.id = 'speed-control-container';
        container.style.position = 'absolute';
        container.style.top = '10px';
        container.style.right = '10px';
        container.style.background = 'white';
        container.style.padding = '10px';
        container.style.borderRadius = '5px';
        container.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        container.style.display = 'flex';
        container.style.gap = '5px';

        const speedButtons = [
            { label: 'x1', speed: 1 },
            { label: 'x1.5', speed: 1.5 },
            { label: 'x2', speed: 2 },
            { label: 'x5', speed: 5 },
        ];

        speedButtons.forEach(({ label, speed }) => {
            const button = document.createElement('button');
            button.textContent = label;
            button.style.padding = '5px 10px';
            button.style.cursor = 'pointer';
            button.addEventListener('click', () => {
                currentAnimator.setSpeed(speed);
            });
            container.appendChild(button);
        });

        return container;
    }

    let currentAnimator;

    function initMarkerAnimation(routePath, unitId, point) {
        const speedControlContainer = createSpeedControlContainer();
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(speedControlContainer);

        currentAnimator = createMarkerAnimator(routePath, unitId, point);
        currentAnimator.start();
    }
    function createMarkerContent(unitId) {
        const markerDiv = document.createElement('div');
        markerDiv.style.position = 'relative';
        markerDiv.style.width = '40px';
        markerDiv.style.height = '40px';

        const markerContainer = document.createElement('div');
        markerContainer.style.width = '100%';
        markerContainer.style.height = '100%';
        markerContainer.style.position = 'absolute';
        markerContainer.style.borderRadius = '50%';
        markerContainer.style.background = 'rgba(0, 123, 255, 0.5)';
        markerContainer.style.animation = 'pulse 1.5s infinite';

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
        <div id="default-img" class="iconDefault rotatable-point">
        <img src="${property.primaryPath}" alt="Primary Image" style="width: 48px; height: 32px;">
        </div>
        <div class="icon">
        <img src="${property.path}" alt="CustomImg" style="width: 100px; height: auto;">
        </div>
        <div class="details">
        <div class="price">Unidad: ${property.unidads_id}</div>
        <div class="address">Detalle: ${property.obs}</div>
        <div class="address" style="display: ${property.speed ? 'block' : 'none'};">Velocidad: ${parseFloat(property.speed).toFixed(2)} km/h</div>
        <div class="address" style="display: ${property.avgSpd ? 'block' : 'none'};">Velocidad Promedio: ${parseFloat(property.avgSpd).toFixed(2)} km/h</div>
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
    
    function añadirMarcador(point, lastRotationAngle){
        
        const property = {
            unidads_id: point.unidads_id || 'N/A',
            unidads_unidad: point.unidads_unidad || 'N/AA', 
            title: point.title,
            obs: point.obs,
            primaryPath: point.primaryPath,
            path: point.path ? '{{asset('storage/')}}/' + point.path : point.primaryPath,
            avgSpd: point.avgSpd,
            speed: point.speed
        };
        
        const content = buildContent(property);
        const iconElement = content.querySelector("#default-img");
        iconElement.style.transform = "rotate(" + lastRotationAngle + "deg)";
        console.log(iconElement, lastRotationAngle);        
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


