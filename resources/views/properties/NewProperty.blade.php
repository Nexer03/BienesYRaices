<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nueva Propiedad</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        #map { height: 400px; width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body class="container mt-5">

<h1>Crear Nueva Propiedad</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="title" class="form-label">Título</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">Tipo</label>
        <select class="form-select" id="type" name="type" required>
            <option value="house">Casa</option>
            <option value="apartment">Departamento</option>
            <option value="land">Terreno</option>
            <option value="office">Oficina</option>
        </select>
    </div>

    {{-- Habitaciones y Baños --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="bedrooms" class="form-label">Habitaciones</label>
            <input type="number" min="0" class="form-control" id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}">
        </div>
        <div class="col-md-6">
            <label for="bathrooms" class="form-label">Baños</label>
            <input type="number" min="0" class="form-control" id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}">
        </div>
    </div>

    <div class="mb-3">
    <label class="form-label">Propósito</label>
    <div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="listing_type" id="rent" value="rent" checked>
            <label class="form-check-label" for="rent">Renta</label>
        </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="listing_type" id="sale" value="sale">
                <label class="form-check-label" for="sale">Venta</label>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Precio</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" required max="99999999.99">
    </div>

    <div class="mb-3">
        <label for="address-input" class="form-label">Dirección</label>
        <input type="text" class="form-control" id="address-input" name="location" placeholder="Escribe la dirección" required>
    </div>

    <div id="map"></div>

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <div class="mb-3">
        <label class="form-label">Imágenes de la Propiedad</label>
        <div>
            <label for="images" class="btn btn-secondary">Añadir Imágenes</label>
            <input type="file" id="images" name="images[]" multiple class="d-none">
        </div>
        <div id="image-preview-container" class="mt-3 row g-3"></div>
    </div>

    <div class="mb-3">
        <h3>Amenidades</h3>
        @foreach($amenityCategories as $category)
            @php
                $saleCategories = [
                    'Cocina y Electrodomésticos',
                    'Exterior y Lote',
                    'Características Interiores',
                    'Servicios y Seguridad'
                ];
                $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
            @endphp
            <div class="amenity-category mt-3" data-type="{{ $type }}">
                <h5>{{ $category->name }}</h5>
                <div class="row mb-2">
                    @foreach($category->amenities as $amenity)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}">
                                <label class="form-check-label" for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Guardar Propiedad</button>
</form>

{{-- =================== SCRIPT UNIFICADO Y CORREGIDO =================== --}}
<script>
    // --- LÓGICA DE PREVISUALIZACIÓN DE IMÁGENES ---
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const fileStore = new DataTransfer();

    imageInput.addEventListener('change', (e) => {
        for (const file of e.target.files) {
            fileStore.items.add(file);
        }
        imageInput.files = fileStore.files;
        renderPreviews();
    });

    function renderPreviews() {
        previewContainer.innerHTML = '';
        Array.from(fileStore.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = () => {
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'col-auto';
                previewWrapper.innerHTML = `
                    <div class="position-relative">
                        <img src="${reader.result}" class="img-thumbnail" style="object-fit: cover; width: 150px; height: 150px;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removeImage(${index})">
                            &times;
                        </button>
                    </div>
                `;
                previewContainer.appendChild(previewWrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    // --- FUNCIÓN CORREGIDA ---
    function removeImage(index) {
        // Creamos un array temporal con los archivos que queremos conservar
        const keptFiles = Array.from(fileStore.files).filter((_, i) => i !== index);

        // Limpiamos el almacén original usando el método correcto
        fileStore.items.clear();

        // Volvemos a añadir los archivos conservados al almacén
        keptFiles.forEach(file => {
            fileStore.items.add(file);
        });

        // Sincronizamos el input del formulario y volvemos a renderizar
        imageInput.files = fileStore.files;
        renderPreviews();
    }

    // --- LÓGICA DE GOOGLE MAPS ---
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
        const geocoder = new google.maps.Geocoder();
        const addressInput = document.getElementById("address-input");
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.bindTo("bounds", map);
        autocomplete.addListener("place_changed", function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;
            map.setCenter(place.geometry.location);
            map.setZoom(16);
            marker.setPosition(place.geometry.location);
            latInput.value = place.geometry.location.lat();
            lonInput.value = place.geometry.location.lng();
        });
        marker.addListener('dragend', function() {
            const pos = marker.getPosition();
            latInput.value = pos.lat();
            lonInput.value = pos.lng();
            geocoder.geocode({ location: pos })
                .then((response) => {
                    addressInput.value = response.results[0] ? response.results[0].formatted_address : "No se pudo encontrar la dirección";
                })
                .catch((e) => console.log("Geocoder failed due to: " + e));
        });
    }

    // --- LÓGICA DEL FORMULARIO (AMENIDADES Y PREVENCIÓN DE ENTER) ---
    document.addEventListener('DOMContentLoaded', function() {
        const listingTypeRadios = document.querySelectorAll('input[name="listing_type"]');
        const amenityCategories = document.querySelectorAll('.amenity-category');

        function toggleAmenities() {
            const selectedType = document.querySelector('input[name="listing_type"]:checked').value;
            amenityCategories.forEach(category => {
                const categoryType = category.dataset.type;
                category.style.display = (categoryType === selectedType) ? 'block' : 'none';
            });
        }
        listingTypeRadios.forEach(radio => radio.addEventListener('change', toggleAmenities));
        toggleAmenities();

        const addressInput = document.getElementById("address-input");
        addressInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                event.preventDefault();
            }
        });
    });
</script>

</body>
</html>
