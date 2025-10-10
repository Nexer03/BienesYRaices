<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Prueba Avanzado</title>
    <style>
    #map {
        height: 700px;
        width: 100%;
    }

    /* Estilo de la mini-tarjeta para precio */
    .price-marker {
        background-color: #FF5722;
        color: white;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: bold;
        font-family: Arial, sans-serif;
        font-size: 14px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    </style>
</head>

<body>
    <h1>Mapa de prueba con marcadores creativos</h1>
    <div id="map"></div>
    <script>
    fetch('/maps-key')
        .then(res => res.json())
        .then(data => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
            script.async = true;
            document.head.appendChild(script);
        });

    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: {
                lat: 20.749757,
                lng: -105.258849
            },
            styles: [{
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{
                        visibility: "off"
                    }]
                },
                {
                    featureType: "transit",
                    elementType: "labels",
                    stylers: [{
                        visibility: "off"
                    }]
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{
                        color: "#ffffff"
                    }]
                }
            ]
        });

        // Marcadores creativos
        const markersData = [{
            pos: {
                lat: 20.7505,
                lng: -105.260
            },
            title: "Casa Azul"
        }, ];

        markersData.forEach(data => {
            new google.maps.Marker({
                position: data.pos,
                map: map,
                title: data.title,
                icon: data.icon
            });
        });

        // Marcador de precio estilo tarjeta
        const priceMarker = new google.maps.Marker({
            position: {
                lat: 20.749757,
                lng: -105.258849
            },
            map: map,
            title: "Alquiler: 4000 MXN",
            label: {
                text: "4000 MXN",
                color: "#fff",
                fontWeight: "bold",
                fontSize: "12px",
                className: "price-marker"
            },
            
        });

        const infoWindow = new google.maps.InfoWindow({
            content: '<div class="price-marker">4000 MXN<br>Casa céntrica, 2 recámaras</div>'
        });

        priceMarker.addListener('click', () => {
            infoWindow.open(map, priceMarker);
        });
    }
    </script>


</body>

</html>