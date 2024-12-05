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
        <!-- <div id="panel"></div> -->
    </div>

</div>
@script    
<script>   
    const toggleSidebar = document.querySelector('#sidebar-toggle');
    toggleSidebar.click();
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    let map, markers = [];
    // console.clear();
    const points = @json($gpsPoints);        
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
        // directionsRenderer.setPanel(document.getElementById("panel"));

        Object.keys(points).forEach(unitId => {
            
            const unitPoints = points[unitId];                
            if (unitPoints.length > 1) {                
                const waypoints = unitPoints.slice(1, -1).map(point => ({
                    location: { lat: point.lat, lng: point.lng },
                    stopover: true
                }));
                const request = {
                    origin: { lat: unitPoints[0].lat, lng: unitPoints[0].lng },
                    destination: { lat: unitPoints[unitPoints.length - 1].lat, lng: unitPoints[unitPoints.length - 1].lng },
                    waypoints: waypoints,
                    optimizeWaypoints: false,
                    travelMode: google.maps.TravelMode.DRIVING
                };
                directionsService.route(request, (result, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.error('Error al obtener la ruta:', status);
                    }
                });
                unitPoints.forEach((point, index) => {
                    if(index === 0 || index === unitPoints.length - 1){
                        const imageElement = document.createElement("img");
                        imageElement.src = "{{ asset('assets/images/thumbnails/img48-32.png') }}";
                        imageElement.alt = `Marker for ${point.title}`;                

                        const marker = new google.maps.marker.AdvancedMarkerElement({
                            id: point.id,
                            position: point,
                            map: map,
                            content: imageElement,
                            title: `${index + 1}. ${point.title}`,
                            gmpClickable: true,
                        });                    
                    }
                });
            }else{                               
                unitPoints.forEach((point, index) => {
                
                const imageElement = document.createElement("img");
                imageElement.src = "{{ asset('assets/images/thumbnails/img48-32.png') }}";
                imageElement.alt = `Marker for ${point.title}`;                

                const marker = new google.maps.marker.AdvancedMarkerElement({
                    id: point.id,
                    position: point,
                    map: map,
                    content: imageElement,
                    title: `${index + 1}. ${point.title}`,
                    gmpClickable: true,
                });

         
                marker.addListener("click", () => {
                    toggleHighlight(marker, point);
                });

            });
            }

            // Añadir marker
            if(points[unitId].length === 1){
                
            } else {
                
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


