<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Preferencias de Búsqueda - SIN BECA NO HAY RENTA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Anti-flash: aplica tema guardado ANTES de pintar la página --}}
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    {{-- Iconos --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Fade suave para toda la página al cambiar de tema */
        html.theme-fade * {
            transition:
                background-color .35s ease,
                color .35s ease,
                border-color .35s ease,
                fill .35s ease;
        }

        /* Animación del botón de tema */
        #theme-toggle {
            transition:
                background-color .25s ease,
                color .25s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        #theme-toggle.theme-bounce {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0, 0, 0, .18);
        }

        #theme-toggle-icon {
            transition: transform .35s ease, opacity .2s ease;
        }

        #theme-toggle-icon.theme-spin {
            transform: rotate(180deg);
        }

        /* Input de dirección en modo oscuro (texto y placeholder visibles) */
        .dark #preference-address-input {
            background-color: #020617; /* slate-950 */
            color: #e5e7eb;
            border-color: #4b5563;
        }
        .dark #preference-address-input::placeholder {
            color: #9ca3af;
        }

        /* Mapa: solo colores, NO tamaños */
        .dark #preference-map {
            background-color: #020617;
            border: 1px solid #4b5563;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col">

    {{-- Header global --}}
    <x-main-header />

    <main class="flex-grow">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            {{-- Título + descripción --}}
            <header class="mb-8">
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 mb-3">
                    <i class="fa-solid fa-sliders text-xs text-blue-700 dark:text-blue-300"></i>
                    <span class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-200">
                        Preferencias personalizadas
                    </span>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold mb-2 text-gray-900 dark:text-gray-50">
                    Mis Preferencias de Búsqueda
                </h1>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-300">
                    Ajusta tu zona, presupuesto y amenidades para que las recomendaciones se adapten mejor a lo que buscas.
                </p>
            </header>

            {{-- Tarjeta principal --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800">
                <form action="{{ route('preferences.update') }}" method="POST" class="p-6 sm:p-8 space-y-8">
                    @csrf
                    @method('PUT') {{-- Actualizamos un solo recurso --}}

                    {{-- Ubicación Preferida (Mapa Interactivo) --}}
                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-900/40">
                                        <i class="fa-solid fa-location-dot text-blue-600 dark:text-blue-300 text-sm"></i>
                                    </span>
                                    Zona de búsqueda preferida
                                </h2>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Define el área donde te gustaría encontrar propiedades.
                                </p>
                            </div>
                        </div>

                        {{-- Campo de búsqueda de dirección --}}
                        <input
                            type="text"
                            id="preference-address-input"
                            placeholder="Escribe una dirección o zona para centrar el mapa (ej. Puerto Vallarta, Nayarit)"
                            class="mb-3 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                   text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                        <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-950/70">
                            <div id="preference-map" style="height: 400px; width: 100%;"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="md:col-span-1">
                                <label for="pref_radius_km"
                                       class="block text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">
                                    Radio de búsqueda (km)
                                </label>
                                <input type="number"
                                       step="0.5"
                                       min="0.5"
                                       id="pref_radius_km"
                                       class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('pref_radius', ($preferences->pref_radius ?? 5000) / 1000) }}">
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 md:col-span-2">
                                <p>
                                    Usaremos este radio alrededor del punto seleccionado como tu zona principal de búsqueda.
                                </p>
                            </div>
                        </div>

                        {{-- Hidden fields --}}
                        <input type="hidden" name="pref_latitude" id="pref_latitude"
                               value="{{ old('pref_latitude', $preferences->pref_latitude) }}">
                        <input type="hidden" name="pref_longitude" id="pref_longitude"
                               value="{{ old('pref_longitude', $preferences->pref_longitude) }}">
                        <input type="hidden" name="pref_radius" id="pref_radius"
                               value="{{ old('pref_radius', $preferences->pref_radius ?? 5000) }}">
                        <input type="hidden" name="preferred_location" id="preferred_location"
                               value="{{ old('preferred_location', $preferences->preferred_location) }}">
                    </section>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    {{-- Rango de Precios --}}
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-900/40">
                                <i class="fa-solid fa-dollar-sign text-emerald-600 dark:text-emerald-300 text-sm"></i>
                            </span>
                            Rango de precios
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="min_price"
                                       class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                    Precio mínimo ($)
                                </label>
                                <input type="number"
                                       step="any"
                                       id="min_price"
                                       name="min_price"
                                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('min_price', $preferences->min_price) }}">
                            </div>
                            <div>
                                <label for="max_price"
                                       class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                    Precio máximo ($)
                                </label>
                                <input type="number"
                                       step="any"
                                       id="max_price"
                                       name="max_price"
                                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('max_price', $preferences->max_price) }}">
                            </div>
                        </div>
                    </section>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    {{-- Tipo de Listado --}}
                    <section class="space-y-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/40">
                                <i class="fa-solid fa-arrow-right-arrow-left text-indigo-600 dark:text-indigo-300 text-xs"></i>
                            </span>
                            Tipo de operación
                        </h2>
                        <div class="flex flex-wrap gap-3">
                            <label class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-full border border-gray-300 dark:border-gray-700 cursor-pointer bg-white dark:bg-gray-900">
                                <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       type="radio"
                                       name="preferred_listing_type"
                                       id="type_rent"
                                       value="rent"
                                       @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'rent')>
                                <span>Renta</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-full border border-gray-300 dark:border-gray-700 cursor-pointer bg-white dark:bg-gray-900">
                                <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       type="radio"
                                       name="preferred_listing_type"
                                       id="type_sale"
                                       value="sale"
                                       @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'sale')>
                                <span>Venta</span>
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-full border border-gray-300 dark:border-gray-700 cursor-pointer bg-white dark:bg-gray-900">
                                <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       type="radio"
                                       name="preferred_listing_type"
                                       id="type_any"
                                       value=""
                                       @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == '')>
                                <span>Cualquiera</span>
                            </label>
                        </div>
                    </section>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    {{-- Habitaciones y Baños Mínimos --}}
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-50 dark:bg-amber-900/40">
                                <i class="fa-solid fa-bed text-amber-600 dark:text-amber-300 text-sm"></i>
                            </span>
                            Características mínimas
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="min_bedrooms"
                                       class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                    Habitaciones mínimas
                                </label>
                                <input type="number"
                                       min="0"
                                       id="min_bedrooms"
                                       name="min_bedrooms"
                                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('min_bedrooms', $preferences->min_bedrooms) }}">
                            </div>
                            <div>
                                <label for="min_bathrooms"
                                       class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                                    Baños mínimos
                                </label>
                                <input type="number"
                                       min="0"
                                       id="min_bathrooms"
                                       name="min_bathrooms"
                                       class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('min_bathrooms', $preferences->min_bathrooms) }}">
                            </div>
                        </div>
                    </section>

                    {{-- Separador --}}
                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    {{-- Amenidades Preferidas (Checkboxes Dinámicos) --}}
                    <section class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-teal-50 dark:bg-teal-900/40">
                                    <i class="fa-solid fa-star text-teal-600 dark:text-teal-300 text-sm"></i>
                                </span>
                                Amenidades preferidas
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Marca lo que consideres importante en tu próximo hogar.
                            </p>
                        </div>

                        @php
                            // Convertimos la cadena guardada (ej: "1,5,12") en un array de IDs
                            $preferredAmenityIds = explode(',', old('preferred_amenities', $preferences->preferred_amenities ?? ''));
                        @endphp

                        <div class="space-y-4">
                            @foreach($amenityCategories as $category)
                                @php
                                    $saleCategories = ['Cocina y Electrodomésticos', 'Exterior y Lote', 'Características Interiores', 'Servicios y Seguridad'];
                                    $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
                                @endphp

                                <div class="amenity-category border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-900/70"
                                     data-type="{{ $type }}">
                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                        {{ $category->name }}
                                    </h5>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($category->amenities as $amenity)
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input
                                                    class="amenity-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    type="checkbox"
                                                    value="{{ $amenity->id }}"
                                                    id="amenity-{{ $amenity->id }}"
                                                    @checked(in_array($amenity->id, $preferredAmenityIds))>
                                                <span>{{ $amenity->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Campo oculto para enviar los IDs seleccionados como cadena --}}
                        <input type="hidden"
                               name="preferred_amenities"
                               id="preferred_amenities_hidden"
                               value="{{ old('preferred_amenities', $preferences->preferred_amenities ?? '') }}">
                    </section>

                    {{-- Botones --}}
                    <div class="pt-5 mt-3 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 shadow-sm hover:shadow-md transition">
                            <i class="fa-solid fa-floppy-disk mr-2 text-xs"></i>
                            Guardar preferencias
                        </button>
                        <a href="{{ url('/') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                                  text-sm font-semibold text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- Footer global --}}
    <x-main-footer />

    {{-- Botón Tema (mismo patrón que otras vistas) --}}
    <button id="theme-toggle"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                   bg-white text-gray-800 hover:bg-gray-100
                   dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
            aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    {{-- Flatpickr para calendario de reservas en el chat (si lo llegas a usar aquí) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    // --- LÓGICA DE GOOGLE MAPS ---
    let map, marker, circle, geocoder;
    const latInput = document.getElementById('pref_latitude');
    const lonInput = document.getElementById('pref_longitude');
    const radiusInput = document.getElementById('pref_radius');      // oculto (m)
    const radiusKmInput = document.getElementById('pref_radius_km'); // visible (km)
    const locationInput = document.getElementById('preferred_location');

    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        if (!window.google || !window.google.maps) {
             const script = document.createElement('script');
             script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initPrefMap&libraries=places,geometry`;
             script.async = true;
             document.head.appendChild(script);
        } else {
             initPrefMap();
        }
      });

    // ================== Google Maps con modo oscuro ==================
    const lightMapStyle = [
        {featureType:"poi",stylers:[{visibility:"off"}]},
        {featureType:"transit",stylers:[{visibility:"off"}]},
        {featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]}
    ];
    const darkMapStyle = [
        {elementType:"geometry",stylers:[{color:"#1f2937"}]},
        {elementType:"labels.text.fill",stylers:[{color:"#93a4b8"}]},
        {elementType:"labels.text.stroke",stylers:[{color:"#1f2937"}]},
        {featureType:"administrative",elementType:"geometry",stylers:[{color:"#334155"}]},
        {featureType:"poi",stylers:[{visibility:"off"}]},
        {featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]},
        {featureType:"road",elementType:"geometry",stylers:[{color:"#2b3647"}]},
        {featureType:"road",elementType:"geometry.stroke",stylers:[{color:"#374151"}]},
        {featureType:"water",elementType:"geometry",stylers:[{color:"#0b1220"}]},
        {featureType:"transit",stylers:[{visibility:"off"}]}
    ];

    function initPrefMap() {
        const initialLat = parseFloat(latInput.value) || 20.6597;
        const initialLng = parseFloat(lonInput.value) || -103.3496;
        const initialRadius = parseInt(radiusInput.value) || 5000;
        const center = { lat: initialLat, lng: initialLng };

        const isDark = document.documentElement.classList.contains('dark');

        map = new google.maps.Map(document.getElementById("preference-map"), {
            center: center,
            zoom: 12,
            styles: isDark ? darkMapStyle : lightMapStyle
        });

        // Exponer mapa y estilos globalmente para el toggle
        window._prefMap = map;
        window._prefMapStyles = { light: lightMapStyle, dark: darkMapStyle };

        marker = new google.maps.Marker({
            map: map,
            position: center,
            draggable: true,
            title: "Arrastra para definir el centro de tu búsqueda"
        });

        circle = new google.maps.Circle({
            map: map,
            center: center,
            radius: initialRadius,
            fillColor: '#2563EB',
            fillOpacity: 0.15,
            strokeColor: '#2563EB',
            strokeOpacity: 0.9,
            strokeWeight: 1,
            editable: false
        });

        geocoder = new google.maps.Geocoder();

        const addressInput = document.getElementById("preference-address-input");
        const autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.bindTo("bounds", map);

        autocomplete.addListener("place_changed", function() {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) {
                console.log("Autocomplete's returned place contains no geometry");
                return;
            }

            const newPos = place.geometry.location;

            map.setCenter(newPos);
            map.setZoom(14);
            marker.setPosition(newPos);
            circle.setCenter(newPos);

            updateLocation(newPos.lat(), newPos.lng());
            updateLocationDescription(newPos);
        });

        marker.addListener('dragend', function() {
            const pos = marker.getPosition();
            updateLocation(pos.lat(), pos.lng());
            circle.setCenter(pos);
            updateLocationDescription(pos);
        });

        radiusKmInput.addEventListener('change', function() {
            const radiusInKm = parseFloat(this.value);
            if (!isNaN(radiusInKm) && radiusInKm > 0) {
                const radiusInMeters = radiusInKm * 1000;
                radiusInput.value = radiusInMeters;
                circle.setRadius(radiusInMeters);
            }
        });
    }

    function updateLocation(lat, lng) {
        latInput.value = lat;
        lonInput.value = lng;
    }

    function updateLocationDescription(position) {
        if (!geocoder) return;
        geocoder.geocode({ location: position })
            .then((response) => {
                if (response.results[0]) {
                    locationInput.value = response.results[0].formatted_address
                        .split(',')
                        .slice(-3)
                        .join(',')
                        .trim();
                }
            })
            .catch((e) => console.log("Geocoder failed: " + e));
    }

    // --- LÓGICA DEL FORMULARIO (amenidades + enter) ---
    document.addEventListener('DOMContentLoaded', function() {
        const listingTypeRadios = document.querySelectorAll('input[name="preferred_listing_type"]');
        const amenityCategories = document.querySelectorAll('.amenity-category');
        const allAmenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
        const hiddenAmenitiesInput = document.getElementById('preferred_amenities_hidden');

        const preferencesForm = document.querySelector('form');
        preferencesForm.addEventListener('keydown', function(event) {
            if ((event.key === 'Enter' || event.keyCode === 13) && event.target.type !== 'submit') {
                event.preventDefault();
            }
        });

        function toggleAmenities() {
            const selectedType = document.querySelector('input[name="preferred_listing_type"]:checked').value;
            amenityCategories.forEach(category => {
                const categoryType = category.dataset.type;
                if (selectedType === '' || categoryType === selectedType) {
                    category.style.display = 'block';
                } else {
                    category.style.display = 'none';
                }
            });
        }

        function updateHiddenAmenities() {
            const selectedIds = [];
            allAmenityCheckboxes.forEach(checkbox => {
                if (checkbox.checked) selectedIds.push(checkbox.value);
            });
            hiddenAmenitiesInput.value = selectedIds.join(',');
        }

        listingTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleAmenities);
        });

        allAmenityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateHiddenAmenities);
        });

        toggleAmenities();

        const addressInputForEnter = document.getElementById("preference-address-input");
        if (addressInputForEnter) {
            addressInputForEnter.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.keyCode === 13) {
                    event.preventDefault();
                }
            });
        }
    });
    </script>

    {{-- Script del botón de tema --}}
    <script>
        (function () {
            const html  = document.documentElement;
            const btn   = document.getElementById('theme-toggle');
            const icon  = document.getElementById('theme-toggle-icon');
            const label = btn?.querySelector('span');

            function setIconAndLabel() {
                const isDark = html.classList.contains('dark');
                if (!icon || !label) return;

                icon.classList.remove('fa-sun', 'fa-moon');
                icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
                label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
            }

            function startPageFade() {
                html.classList.add('theme-fade');
                setTimeout(() => html.classList.remove('theme-fade'), 400);
            }

            function animateButton() {
                if (!btn || !icon) return;
                btn.classList.add('theme-bounce');
                icon.classList.add('theme-spin');
                setTimeout(() => {
                    btn.classList.remove('theme-bounce');
                    icon.classList.remove('theme-spin');
                }, 350);
            }

            function apply(mode) {
                const isDark = mode === 'dark';
                startPageFade();
                html.classList.toggle('dark', isDark);
                try {
                    localStorage.setItem('theme', mode);
                } catch (e) {}
                setIconAndLabel();
                animateButton();

                // Actualizar estilos del mapa si existe
                if (window._prefMap && window._prefMapStyles) {
                    window._prefMap.setOptions({
                        styles: isDark ? window._prefMapStyles.dark : window._prefMapStyles.light
                    });
                }
            }

            // Inicializar icono/texto según estado actual
            setIconAndLabel();

            btn?.addEventListener('click', () => {
                const next = html.classList.contains('dark') ? 'light' : 'dark';
                apply(next);
            });
        })();
    </script>
</body>
</html>
