<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Preferencias de Búsqueda</title>
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
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 min-h-screen">

    <main class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-2">Mis Preferencias de Búsqueda</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            Completa tus preferencias para recibir mejores recomendaciones.
        </p>
        <div class="border-t border-gray-200 dark:border-gray-700 mb-6"></div>

        <form action="{{ route('preferences.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT') {{-- We use PUT because we're updating (or creating) a single resource --}}

            {{-- Ubicación Preferida (Mapa Interactivo) --}}
            <section class="space-y-3">
                <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                    Zona de Búsqueda Preferida
                </label>

                {{-- Campo de búsqueda de dirección --}}
                <input
                    type="text"
                    id="preference-address-input"
                    placeholder="Escribe una dirección o zona para centrar el mapa"
                    class="mb-2 block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                           text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >

                <div id="preference-map"
                     style="height: 400px; width: 100%; margin-bottom: 15px;"></div>

                <div class="space-y-2">
                    <label for="pref_radius_km"
                           class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                        Radio de Búsqueda (km)
                    </label>
                    <input type="number"
                           step="0.5"
                           min="0.5"
                           id="pref_radius_km"
                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900
                                  text-gray-900 dark:text-gray-100 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('pref_radius', ($preferences->pref_radius ?? 5000) / 1000) }}">

                    <input type="hidden" name="pref_latitude" id="pref_latitude"
                           value="{{ old('pref_latitude', $preferences->pref_latitude) }}">
                    <input type="hidden" name="pref_longitude" id="pref_longitude"
                           value="{{ old('pref_longitude', $preferences->pref_longitude) }}">
                    <input type="hidden" name="pref_radius" id="pref_radius"
                           value="{{ old('pref_radius', $preferences->pref_radius ?? 5000) }}">
                    <input type="hidden" name="preferred_location" id="preferred_location"
                           value="{{ old('preferred_location', $preferences->preferred_location) }}">
                </div>
            </section>

            {{-- Rango de Precios --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Rango de Precios</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_price"
                               class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                            Precio Mínimo ($)
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
                            Precio Máximo ($)
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

            {{-- Tipo de Listado --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Tipo de Operación</h2>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               type="radio"
                               name="preferred_listing_type"
                               id="type_rent"
                               value="rent"
                               @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'rent')>
                        <span>Renta</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               type="radio"
                               name="preferred_listing_type"
                               id="type_sale"
                               value="sale"
                               @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'sale')>
                        <span>Venta</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
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

            {{-- Habitaciones y Baños Mínimos --}}
            <section>
                <h2 class="text-lg font-semibold mb-3">Características mínimas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_bedrooms"
                               class="block text-sm font-medium text-gray-800 dark:text-gray-200">
                            Habitaciones Mínimas
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
                            Baños Mínimos
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

            {{-- Amenidades Preferidas (Checkboxes Dinámicos) --}}
            <section class="space-y-4">
                <h2 class="text-lg font-semibold">Amenidades Preferidas</h2>
                @php
                    // Convertimos la cadena guardada (ej: "1,5,12") en un array de IDs
                    $preferredAmenityIds = explode(',', old('preferred_amenities', $preferences->preferred_amenities ?? ''));
                @endphp

                @foreach($amenityCategories as $category)
                    @php
                        $saleCategories = ['Cocina y Electrodomésticos', 'Exterior y Lote', 'Características Interiores', 'Servicios y Seguridad'];
                        $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
                    @endphp

                    <div class="amenity-category border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-900"
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

                {{-- Campo oculto para enviar los IDs seleccionados como cadena --}}
                <input type="hidden"
                       name="preferred_amenities"
                       id="preferred_amenities_hidden"
                       value="{{ old('preferred_amenities', $preferences->preferred_amenities ?? '') }}">
            </section>

            {{-- Botones --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                    Guardar Preferencias
                </button>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600
                          text-sm font-semibold text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </main>

    {{-- Botón Tema (mismo que en la home) --}}
    <button id="theme-toggle"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                   bg-white text-gray-800 hover:bg-gray-100
                   dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
            aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

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

    function initPrefMap() {
        const initialLat = parseFloat(latInput.value) || 20.6597;
        const initialLng = parseFloat(lonInput.value) || -103.3496;
        const initialRadius = parseInt(radiusInput.value) || 5000;
        const center = { lat: initialLat, lng: initialLng };

        map = new google.maps.Map(document.getElementById("preference-map"), {
            center: center,
            zoom: 12
        });

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
            fillColor: '#AA0000',
            fillOpacity: 0.2,
            strokeColor: '#AA0000',
            strokeOpacity: 0.8,
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

    {{-- Script del botón de tema (copiado del home, adaptado) --}}
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
