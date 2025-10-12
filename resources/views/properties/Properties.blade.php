<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mapa de Propiedades</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  #map { height: 100vh; width: 100%; }
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
<body class="bg-gray-50">

<div id="map" class="rounded-2xl shadow-lg overflow-hidden w-full max-w-6xl mx-auto mt-6"></div>

<script>
const properties = @json($properties);

fetch('/maps-key')
  .then(res => res.json())
  .then(data => {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
    script.async = true;
    document.head.appendChild(script);
  });

function initMap() {
    const defaultLocation = { lat: 20.749757, lng: -105.258849 };
    const map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLocation,
        zoom: 14
    });

    const infoWindow = new google.maps.InfoWindow();

    properties.forEach(prop => {
        if (prop.latitude && prop.longitude) {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(prop.latitude), lng: parseFloat(prop.longitude) },
                map: map,
                label: {
                    text: `$${prop.price}`,
                    className: 'price-marker'
                },
                title: prop.title
            });

            marker.addListener('click', () => {
                infoWindow.setContent(`
                    <div class="price-marker">
                        <strong>${prop.title}</strong><br>
                        ${prop.description}<br>
                        Precio: $${prop.price}
                    </div>
                `);
                infoWindow.open(map, marker);
            });
        }
    });
}
</script>

</body>
</html>
