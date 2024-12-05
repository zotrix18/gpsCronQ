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

    <div wire:ignore>
        <div id="map" style="height: 600px; width: 100%;"></div>
    </div>

</div>
@script    

    <script>   
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    let map, markers = [];
    // console.clear();
    const points = @json($gpsPoints);
    const beachFlagImg = document.createElement("img");

    beachFlagImg.src =
    "{{ asset('assets/images/thumbnails/img48-32.png') }}";
        // "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
    const center = { lat: -27.47975561390458, lng: -58.81530992766529 };
    console.log(points);
   

    function initMaps() {
        map = new google.maps.Map(document.getElementById('map'), {
            center,
            zoom: 14,
            mapId: "DEMO_MAP_ID",
        });

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: "#FF0000",
                strokeOpacity: 1,
                strokeWeight: 3
            }
        });
        directionsRenderer.setMap(map);

        Object.keys(points).forEach(unitId => {
            const unitPoints = points[unitId];            
            // If unit has more than one point, create directions
            if (unitPoints.length > 1) {
                // Ordenar puntos de más antiguo a más reciente
                const sortedPoints = unitPoints.sort((a, b) => a.id - b.id);

                // Crear solicitud de ruta
                const waypoints = sortedPoints.slice(1, -1).map(point => ({
                    location: { lat: point.lat, lng: point.lng },
                    stopover: true
                }));

                const request = {
                    origin: { lat: sortedPoints[0].lat, lng: sortedPoints[0].lng },
                    destination: { lat: sortedPoints[sortedPoints.length - 1].lat, lng: sortedPoints[sortedPoints.length - 1].lng },
                    waypoints: waypoints,
                    optimizeWaypoints: false,
                    travelMode: google.maps.TravelMode.DRIVING
                };

                // Solicitar ruta
                directionsService.route(request, (result, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.error('Error al obtener la ruta:', status);
                    }
                });
            }else{
                // Si solo hay un punto, mostrar el marker
                
            }

            // Añadir marker
            if(unitPoints.length === 1){
                unitPoints.forEach((point, index) => {
                    let marker = new google.maps.marker.AdvancedMarkerElement({
                        id: point.id,
                        position: point,
                        map: map,
                        content: beachFlagImg,
                        // icon: {
                        //     url: "{{ asset('assets/images/thumbnails/DeU8RivW4AA5j16.png') }}",
                        //     scaledSize: new google.maps.Size(32, 32),
                        // },
                        title: point.title,
                        // label: { text: point.label, color: 'transparent' },
                    });
        
                    markers.push(marker);
                });
            } else {
                unitPoints.forEach((point, index) => {
                    if(index === 0 || index === unitPoints.length - 1){
                        const marker = new google.maps.marker.AdvancedMarkerElement({
                            id: point.id,
                            position: point,
                            map: map,
                            content: beachFlagImg,
                            // icon: {
                            //     url: "{{ asset('assets/images/thumbnails/DeU8RivW4AA5j16.png') }}",
                            //     scaledSize: new google.maps.Size(32, 32),
                            // },
                            title: point.title,
                            // label: { text: point.label, color: 'transparent' },
                        });
            
                        markers.push(marker);                    
                    }
                });
            }
        });
    }


    function updateMarkers() {         
        console.log(markers[0]);
        
        markers.forEach((marker, index) => {        
            
            points[index] = { lat: newLat, lng: newLng };
            marker.setPosition(points[index]);
        });
    }

    

initMaps();
</script>
@endscript


