<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva Propiedad</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  #map { height: 400px; width: 100%; margin-bottom: 20px; }
</style>
</head>
<body class="p-6 bg-gray-50">

<h1 class="text-2xl font-bold mb-4">Agregar Nueva Propiedad</h1>

<form method="POST" action="{{ route('properties.store') }}">
    @csrf
    <div class="mb-4">
        <label class="block font-medium mb-1">Título</label>
        <input type="text" name="title" class="border p-2 w-full" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Descripción</label>
        <textarea name="description" class="border p-2 w-full" rows="4"></textarea>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Tipo</label>
        <select name="type" class="border p-2 w-full" required>
            <option value="house">Casa</option>
            <option value="apartment">Departamento</option>
            <option value="land">Terreno</option>
            <option value="office">Oficina</option>
        </select>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Precio</label>
        <input type="number" name="price" class="border p-2 w-full" required>
    </div>

    <div class="mb-4">
        <label class="block font-medium mb-1">Dirección</label>
        <input id="address-input" type="text" name="location" class="border p-2 w-full" placeholder="Escribe la dirección" required>
    </div>

    <div id="map"></div>

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar Propiedad</button>
</form>

<script>
fetch('/maps-key')
  .then(res => res.json())
  .then(data => {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
    script.async = true;
    document.head.appendChild(script);
  });

function initMap() {
    const defaultLocation = { lat: 20.749757, lng: -105.258849 };
    const map = new google.maps.Map(document.getElementById("map"), { center: defaultLocation, zoom: 14 });
    const marker = new google.maps.Marker({ map: map, draggable: true, position: defaultLocation });

    const input = document.getElementById("address-input");
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo("bounds", map);

    autocomplete.addListener("place_changed", function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        map.setCenter(place.geometry.location);
        map.setZoom(16);
        marker.setPosition(place.geometry.location);
        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();
    });

    marker.addListener('dragend', function() {
        const pos = marker.getPosition();
        document.getElementById('latitude').value = pos.lat();
        document.getElementById('longitude').value = pos.lng();
    });
}
</script>

</body>
</html>
