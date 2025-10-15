<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- El título de la página será el título de la propiedad --}}
    <title>{{ $property->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map { height: 400px; width: 100%; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    {{-- Puedes incluir aquí tu header de navegación si lo tienes en un componente separado --}}

    <main class="max-w-4xl mx-auto mt-10 px-6">

        <div class="mb-4">
            <h1 class="text-3xl font-bold">{{ $property->title }}</h1>
            <p class="text-md text-gray-600 mt-1">{{ $property->location }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-6">
            @forelse ($property->images as $image)
                <div class="overflow-hidden rounded-lg">
                    <img src="{{ asset('storage/' . $image->image_path) }}"
                         alt="Imagen de {{ $property->title }}"
                         class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300 cursor-pointer">
                </div>
            @empty
                <div class="col-span-full bg-gray-200 h-64 flex items-center justify-center rounded-lg">
                    <p class="text-gray-500">No hay imágenes disponibles.</p>
                </div>
            @endforelse
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Descripción</h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ $property->description ?? 'No hay descripción disponible.' }}
                </p>

                <h2 class="text-2xl font-semibold border-b pb-2 mt-8 mb-4">Lo que ofrece este lugar</h2>

                @php
                    $groupedAmenities = $property->amenities->groupBy('category.name');
                @endphp

                @forelse ($groupedAmenities as $categoryName => $amenities)
                    <div class="mt-4">
                    <h4 class="font-semibold text-lg mb-2">{{ $categoryName }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                @foreach ($amenities as $amenity)
                    <div class="text-gray-700">{{ $amenity->name }}</div>
                @endforeach
            </div>
        </div>
            @empty
                <p class="text-gray-500">No se especificaron amenidades.</p>
            @endforelse
            </div>

            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow-md border sticky top-28">
                    <p class="text-2xl font-bold">${{ number_format($property->price, 2) }}</p>
                    <p class="text-gray-500">Precio de renta/venta</p>
                    <button class="w-full bg-blue-500 text-white py-3 rounded-lg mt-4 hover:bg-blue-600 transition font-semibold">
                        Contactar al Agente
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Ubicación</h2>
            <div id="map"></div>
        </div>

    </main>

    <script>
    // Pasamos las coordenadas desde PHP (Blade) a JavaScript
    const propertyLocation = {
        lat: {{ $property->latitude }},
        lng: {{ $property->longitude }}
    };

    // Hacemos el fetch de la API Key
    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
        script.async = true;
        document.head.appendChild(script);
      });

    // Función que se llama cuando la API de Google Maps está lista
    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: propertyLocation,
            zoom: 16 // Un zoom más cercano para ver el detalle
        });

        // Creamos un marcador en la ubicación de la propiedad
        const marker = new google.maps.Marker({
            map: map,
            position: propertyLocation,
            title: "{{ $property->title }}" // El título que aparece al pasar el mouse
        });
    }
    </script>
</body>
</html>
