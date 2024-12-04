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
    let map, markers = [];
    const points = @json($gpsPoints);
    console.log(points);

    // const points = [
    //     { lat: -27.4738946124587, lng: -58.83626027097935, title: 'Movil #2234', label: 'Movil #2234<br>Camion JLP-123' },
    //     { lat: -27.47958317670583, lng: -58.84026149943679, title: 'Movil #2244', label: 'Movil #2244<br>Camion KLI-456' },
    //     { lat: -27.464276593354278, lng: -58.839145700496516, title: 'Movil #2454', label: 'Movil #2454<br>Camion HLP-789' },
    //     { lat: -27.478136376736884, lng: -58.796831171453995, title: 'Movil #2534', label: 'Movil #2534<br>Camion JKL-012' },
    //     { lat: -27.485979327804785, lng: -58.82953266347266, title: 'Movil #2234', label: 'Movil #2234<br>Camion LPK-345' },
    //     { lat: -27.464276593354278, lng: -58.78103832491478, title: 'Movil #2897', label: 'Movil #2897<br>Camion HJK-678' }
    // ];

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -27.47975561390458, lng: -58.81530992766529 },
            zoom: 14,
        });

        // points.forEach((point, index) => {
        //     console.log(point);
            
        const marker = new google.maps.Marker({
            id: points[0].id ,  
            position: points[0],
            map: map,
            icon: {
                url: "{{ asset('assets/images/thumbnails/DeU8RivW4AA5j16.png') }}",
                scaledSize: new google.maps.Size(32, 32),
            },
            title: points[0].title,
            label: { text: points[0].label, color: 'transparent' },
        });
        const infoWindow = new google.maps.InfoWindow();

            // marker.addListener("mouseover", () => {
            //     infoWindow.close();
            //     infoWindow.setContent(marker.getLabel().text);
            //     infoWindow.open(marker.getMap(), marker);
            // });
            // marker.addListener("mouseout", () => {
            //     infoWindow.close();
            // });
        markers.push(marker);
        // window.setTimeout(() => marker.setAnimation(google.maps.Animation.DROP), index * 200);
        // });
        setInterval(updateMarkers, 1500);
    }


    function updateMarkers() {         
        console.log(markers[0]);
        
        markers.forEach((marker, index) => {        
            
            points[index] = { lat: newLat, lng: newLng };
            marker.setPosition(points[index]);
        });
    }

    

initMap();
</script>
@endscript


