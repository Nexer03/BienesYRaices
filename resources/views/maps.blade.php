<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mapa con Marcador Minimalista</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    #map {
      height: 100vh;
      width: 100%;
    }

    /* Estilo del marcador de precio */
    .price-marker {
      background-color: white;
      color: #000;
      padding: 6px 12px;
      border-radius: 9999px;
      font-weight: 600;
      font-size: 14px;
      font-family: 'Inter', sans-serif;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      border: 1px solid #e5e7eb;
    }
  </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

  <div id="map" class="rounded-2xl shadow-lg overflow-hidden w-full max-w-6xl"></div>

  <script>
    
    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
        script.async = true;
        document.head.appendChild(script);
      });

    // Inicializar el mapa
    function initMap() {
      const ubicacion = { lat: 20.749757, lng: -105.258849 };

      const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: ubicacion,
        disableDefaultUI: true, 
        styles: [
          { featureType: "poi", stylers: [{ visibility: "off" }] },
          { featureType: "transit", stylers: [{ visibility: "off" }] },
          { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
        ]
      });

      // Marcador blanco con texto negro
      new google.maps.Marker({
        position: ubicacion,
        map: map,
        label: {
          text: "4000 MXN",
          className: "price-marker"
        }
      });
    }
  </script>
</body>
</html>
