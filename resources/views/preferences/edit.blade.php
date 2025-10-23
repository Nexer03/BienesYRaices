<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Preferencias de Búsqueda</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">

    <h1>Mis Preferencias de Búsqueda</h1>
    <p class="text-muted">Completa tus preferencias para recibir mejores recomendaciones.</p>
    <hr>

    <form action="{{ route('preferences.update') }}" method="POST">
        @csrf
        @method('PUT') {{-- We use PUT because we're updating (or creating) a single resource --}}

        {{-- Ubicación Preferida (Mapa Interactivo) --}}
        <div class="mb-3">
            <label class="form-label">Zona de Búsqueda Preferida</label>

            {{-- NUEVO: Campo de búsqueda de dirección --}}
            <input type="text" class="form-control mb-2" id="preference-address-input" placeholder="Escribe una dirección o zona para centrar el mapa">

            <div id="preference-map" style="height: 400px; width: 100%; margin-bottom: 15px;"></div>

            {{-- Campos de radio y ocultos (sin cambios) --}}
            <label for="pref_radius_km" class="form-label">Radio de Búsqueda (km)</label>
            <input type="number" step="0.5" min="0.5" class="form-control" id="pref_radius_km" value="{{ old('pref_radius', ($preferences->pref_radius ?? 5000) / 1000) }}">
            <input type="hidden" name="pref_latitude" id="pref_latitude" value="{{ old('pref_latitude', $preferences->pref_latitude) }}">
            <input type="hidden" name="pref_longitude" id="pref_longitude" value="{{ old('pref_longitude', $preferences->pref_longitude) }}">
            <input type="hidden" name="pref_radius" id="pref_radius" value="{{ old('pref_radius', $preferences->pref_radius ?? 5000) }}">
            <input type="hidden" name="preferred_location" id="preferred_location" value="{{ old('preferred_location', $preferences->preferred_location) }}">
        </div>

        {{-- Rango de Precios --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="min_price" class="form-label">Precio Mínimo ($)</label>
                <input type="number" step="any" class="form-control" id="min_price" name="min_price" value="{{ old('min_price', $preferences->min_price) }}">
            </div>
            <div class="col-md-6">
                <label for="max_price" class="form-label">Precio Máximo ($)</label>
                <input type="number" step="any" class="form-control" id="max_price" name="max_price" value="{{ old('max_price', $preferences->max_price) }}">
            </div>
        </div>

        {{-- Tipo de Listado --}}
        <div class="mb-3">
            <label class="form-label">Tipo de Operación</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="preferred_listing_type" id="type_rent" value="rent" @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'rent')>
                    <label class="form-check-label" for="type_rent">Renta</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="preferred_listing_type" id="type_sale" value="sale" @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == 'sale')>
                    <label class="form-check-label" for="type_sale">Venta</label>
                </div>
                 <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="preferred_listing_type" id="type_any" value="" @checked(old('preferred_listing_type', $preferences->preferred_listing_type) == '')>
                    <label class="form-check-label" for="type_any">Cualquiera</label>
                </div>
            </div>
        </div>

        {{-- Habitaciones y Baños Mínimos --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="min_bedrooms" class="form-label">Habitaciones Mínimas</label>
                <input type="number" min="0" class="form-control" id="min_bedrooms" name="min_bedrooms" value="{{ old('min_bedrooms', $preferences->min_bedrooms) }}">
            </div>
            <div class="col-md-6">
                <label for="min_bathrooms" class="form-label">Baños Mínimos</label>
                <input type="number" min="0" class="form-control" id="min_bathrooms" name="min_bathrooms" value="{{ old('min_bathrooms', $preferences->min_bathrooms) }}">
            </div>
        </div>

        {{-- Amenidades Preferidas (Checkboxes Dinámicos) --}}
        <div class="mb-3">
            <label class="form-label">Amenidades Preferidas</label>
            @php
                // Convertimos la cadena guardada (ej: "1,5,12") en un array de IDs
                $preferredAmenityIds = explode(',', old('preferred_amenities', $preferences->preferred_amenities ?? ''));
            @endphp

            @foreach($amenityCategories as $category)
                {{-- Lógica para asignar el tipo (rent/sale) a cada categoría --}}
                @php
                    $saleCategories = ['Cocina y Electrodomésticos', 'Exterior y Lote', 'Características Interiores', 'Servicios y Seguridad'];
                    $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
                @endphp

                <div class="amenity-category mt-3" data-type="{{ $type }}">
                    <h5>{{ $category->name }}</h5>
                    <div class="row mb-2">
                        @foreach($category->amenities as $amenity)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input amenity-checkbox"
                                        type="checkbox"
                                        value="{{ $amenity->id }}"
                                        id="amenity-{{ $amenity->id }}"
                                        {{-- Marcamos el checkbox si su ID está en el array de preferencias --}}
                                        @checked(in_array($amenity->id, $preferredAmenityIds))>
                                        <label class="form-check-label" for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Campo oculto para enviar los IDs seleccionados como cadena --}}
            <input type="hidden" name="preferred_amenities" id="preferred_amenities_hidden" value="{{ old('preferred_amenities', $preferences->preferred_amenities ?? '') }}">
        </div>


        <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
        <a href="{{ url('/') }}" class="btn btn-secondary">Cancelar</a>
    </form>

    <script>
    // --- LÓGICA DE GOOGLE MAPS ---
    let map, marker, circle, geocoder; // Make map elements globally accessible within the script
    const latInput = document.getElementById('pref_latitude');
    const lonInput = document.getElementById('pref_longitude');
    const radiusInput = document.getElementById('pref_radius'); // Input oculto (metros)
    const radiusKmInput = document.getElementById('pref_radius_km'); // Input visible (km)
    const locationInput = document.getElementById('preferred_location'); // Input oculto para descripción (opcional)

    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        if (!window.google || !window.google.maps) { // Evita cargar el script si ya existe
             const script = document.createElement('script');
             script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initPrefMap&libraries=places,geometry`;
             script.async = true;
             document.head.appendChild(script);
        } else {
             initPrefMap(); // Si ya está cargado, solo inicializa
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

    // --- NUEVO: Autocompletado para el input de dirección ---
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

        // Mover mapa, marcador y círculo a la nueva ubicación
        map.setCenter(newPos);
        map.setZoom(14); // O ajusta el zoom como prefieras
        marker.setPosition(newPos);
        circle.setCenter(newPos);

        // Actualizar campos ocultos
        updateLocation(newPos.lat(), newPos.lng());
        updateLocationDescription(newPos); // Actualiza la descripción textual
    });
    // --- FIN Autocompletado ---


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
        if (!geocoder) return; // Make sure geocoder is initialized
        geocoder.geocode({ location: position })
            .then((response) => {
                if (response.results[0]) {
                    locationInput.value = response.results[0].formatted_address.split(',').slice(-3).join(',').trim();
                }
            })
            .catch((e) => console.log("Geocoder failed: " + e));
    }

    // --- LÓGICA DEL FORMULARIO (SE EJECUTA CUANDO EL HTML ESTÁ LISTO) ---
    document.addEventListener('DOMContentLoaded', function() {
        const listingTypeRadios = document.querySelectorAll('input[name="preferred_listing_type"]');
        const amenityCategories = document.querySelectorAll('.amenity-category');
        const allAmenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
        const hiddenAmenitiesInput = document.getElementById('preferred_amenities_hidden');
        const preferencesForm = document.querySelector('form'); // Selecciona el primer formulario de la página
            preferencesForm.addEventListener('keydown', function(event) {
                // Si la tecla es Enter Y el elemento NO es un botón de submit
                if ((event.key === 'Enter' || event.keyCode === 13) && event.target.type !== 'submit') {
                    event.preventDefault(); // Evita la acción por defecto (enviar)
                }
            });

        // --- Función para filtrar categorías ---
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

        // --- Función para actualizar el input oculto ---
        function updateHiddenAmenities() {
            const selectedIds = [];
            allAmenityCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedIds.push(checkbox.value);
                }
            });
            hiddenAmenitiesInput.value = selectedIds.join(',');
        }

        // --- Event Listeners ---
        listingTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleAmenities);
        });

        allAmenityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateHiddenAmenities);
        });

        // --- Ejecución Inicial ---
        toggleAmenities(); // Filtra al cargar

        // --- Prevenir que 'Enter' envíe el formulario ---
        // Address input might not exist if map isn't used, check first
        const addressInputForEnter = document.getElementById("address-input");
        if (addressInputForEnter) {
            addressInputForEnter.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.keyCode === 13) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
</body>
</html>
