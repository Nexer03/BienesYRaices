<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- El título de la página será el título de la propiedad --}}
    <title>{{ $property->title }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
    }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- Puedes incluir aquí tu header de navegación si lo tienes en un componente separado --}}

    <main class="max-w-4xl mx-auto mt-10 px-6">

        <div class="mb-4">
            <h1 class="text-3xl font-bold">{{ $property->title }}</h1>
            <p class="text-md text-gray-600 mt-1">{{ $property->location }}</p>
        </div>

        <div class="mb-6">
            @if ($property->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 rounded-lg overflow-hidden" style="max-height: 500px;">
                    {{-- Imagen Principal (Primera Imagen) --}}
                    <a href="{{ asset('storage/' . $property->images->first()->image_path) }}"
                    data-lightbox="property-gallery"
                    data-title="{{ $property->title }}"
                    class="col-span-2 row-span-2 relative group"> {{-- Añadimos 'relative group' --}}
                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                            alt="Imagen principal de {{ $property->title }}"
                            class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition">
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div> {{-- Overlay al hover --}}
                    </a>

                    {{-- Imágenes Secundarias (de la 2da a la 4ta) --}}
                    @foreach ($property->images->slice(1)->take(3) as $image) {{-- Tomamos solo 3 --}}
            <a href="{{ asset('storage/' . $image->image_path) }}"
            data-lightbox="property-gallery"
            data-title="{{ $property->title }}"
            class="relative group">
                {{-- LÍNEA CORREGIDA --}}
                <img src="{{ asset('storage/' . $image->image_path) }}"
                    alt="Imagen de {{ $property->title }}"
                    class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </a>
        @endforeach

            {{-- QUINTA IMAGEN: Con efecto de más fotos (si existe) --}}
            @if ($property->images->count() >= 5) {{-- Si hay al menos 5 imágenes --}}
                @php
                    $fifthImage = $property->images->slice(4)->first(); // Obtenemos la quinta imagen
                @endphp
                <div class="relative group cursor-pointer"
                     onclick="document.querySelector('[data-lightbox=\'property-gallery\']').click();"> {{-- Hacemos click en la primera para abrir el lightbox --}}
                    <img src="{{ asset('storage/' . $fifthImage->image_path) }}"
                         alt="Ver más imágenes de {{ $property->title }}"
                         class="w-full h-full object-cover filter grayscale hover:filter-none transition-all duration-300">

                    {{-- Overlay oscuro con texto e ícono --}}
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white text-lg font-semibold opacity-100 group-hover:opacity-0 transition-opacity duration-300">
                        <svg class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>+ {{ $property->images->count() - 4 }}</span> {{-- Muestra el número de imágenes restantes --}}
                        <span>fotos</span>
                    </div>
                </div>
            @endif

            {{-- Enlaces ocultos para el resto de las imágenes (para que Lightbox las tenga todas) --}}
            @if ($property->images->count() > 5)
                @foreach ($property->images->slice(5) as $image)
                    <a href="{{ asset('storage/' . $image->image_path) }}"
                       data-lightbox="property-gallery"
                       data-title="{{ $property->title }}"
                       class="hidden"></a>
                @endforeach
            @endif
        </div>
    @else
        {{-- Mensaje si no hay imágenes --}}
        <div class="col-span-full bg-gray-200 h-64 flex items-center justify-center rounded-lg">
                    <p class="text-gray-500">No hay imágenes disponibles.</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                {{-- NUEVA SECCIÓN: Habitaciones y Baños --}}
        <div class="flex space-x-4 text-gray-700 border-t border-b py-3 mb-4">
            @if($property->bedrooms)
                <span>&#128719;️ {{ $property->bedrooms }} Habitaciones</span> {{-- Ícono de cama --}}
            @endif
            @if($property->bathrooms)
                <span>&#128705; {{ $property->bathrooms }} Baños</span> {{-- Ícono de baño --}}
            @endif
        </div>
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

                    {{-- Lógica para mostrar si es Renta o Venta --}}
                    @if($property->listing_type == 'rent')
                    <p class="text-gray-500">Precio de renta</p>
                    @elseif($property->listing_type == 'sale')
                    <p class="text-gray-500">Precio de venta</p>
                    @endif

                    <button
                        class="w-full bg-blue-500 text-white py-3 rounded-lg mt-4 hover:bg-blue-600 transition font-semibold">
                        Contactar al Agente
                    </button>
                    <button onclick="openVisitCalendar()"
                        class="w-full bg-blue-500 text-white py-3 rounded-lg mt-4 hover:bg-blue-600 transition font-semibold">
                        Agendar una visita
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Ubicación</h2>
            <div id="map"></div>
        </div>

    </main>
     <div id="visitModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; min-width: 300px;">
            <h3 class="text-lg font-semibold mb-4">Selecciona fecha y hora para tu visita</h3>
            <input type="datetime-local" id="visitDateTime" class="w-full p-2 border rounded mb-4">
            <div class="flex gap-2">
                <button onclick="scheduleVisit()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Agendar Visita
                </button>
                <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

     <script>
    // Pasamos las coordenadas desde PHP (Blade) a JavaScript - CORREGIDO
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
            zoom: 16
        });

        const marker = new google.maps.Marker({
            map: map,
            position: propertyLocation,
            title: "{{ $property->title }}"
        });
    }

    // FUNCIONES DEL MODAL - CORREGIDAS
    function openVisitCalendar() {
        // Establecer fecha mínima (hoy) y fecha por defecto (2 días después)
        const now = new Date();
        const defaultDate = new Date();
        defaultDate.setDate(now.getDate() + 2);
        defaultDate.setHours(10, 0, 0, 0);

        const dateTimeInput = document.getElementById('visitDateTime');
        dateTimeInput.min = now.toISOString().slice(0, 16);
        dateTimeInput.value = defaultDate.toISOString().slice(0, 16);

        document.getElementById('visitModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('visitModal').style.display = 'none';
    }

    function scheduleVisit() {
        const visitDate = document.getElementById('visitDateTime').value;

        if (!visitDate) {
            alert('Por favor selecciona una fecha y hora');
            return;
        }


        fetch('/visits', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                property_id: {{ $property->id }},
                agent_id: {{ $property->user_id }},
                visit_date: visitDate
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Visita agendada. Espera confirmación del agente.');
                closeModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agendar la visita');
        });
    }
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    {{-- Add Lightbox JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
</body>

</html>
