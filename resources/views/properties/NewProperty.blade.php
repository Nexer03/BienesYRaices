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
        <label for="price" class="form-label" id="price-label">Precio por día (MXN)</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" required max="99999999.99">
    </div>

    <div class="mb-3">
        <label for="address-input" class="form-label">Dirección</label>
        <input type="text" class="form-control" id="address-input" name="location" placeholder="Escribe la dirección" required>
    </div>

   <div class="mb-3">
    <label for="city" class="form-label">Ciudad</label>
    <input type="text" id="city" class="form-control" readonly placeholder="La ciudad se rellenará automáticamente">
    <input type="hidden" name="city" id="city-hidden">
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

<script>
    // --- Previsualización de imágenes ---
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
                const wrapper = document.createElement('div');
                wrapper.className = 'col-auto';
                wrapper.innerHTML = `
                    <div class="position-relative">
                        <img src="${reader.result}" class="img-thumbnail" style="object-fit: cover; width: 150px; height: 150px;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removeImage(${index})">&times;</button>
                    </div>
                `;
                previewContainer.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeImage(index) {
        const keptFiles = Array.from(fileStore.files).filter((_, i) => i !== index);
        fileStore.items.clear();
        keptFiles.forEach(file => fileStore.items.add(file));
        imageInput.files = fileStore.files;
        renderPreviews();
    }

    // --- Google Maps ---
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
        const cityInput = document.getElementById("city");
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

            const components = place.address_components;
            const city = components.find(c => c.types.includes("locality"))?.long_name
                       || components.find(c => c.types.includes("administrative_area_level_2"))?.long_name
                       || "";
            cityInput.value = city;        // para mostrar al usuario
document.getElementById('city-hidden').value = city; // para enviar en el formulario
        });

        marker.addListener('dragend', function() {
            const pos = marker.getPosition();
            latInput.value = pos.lat();
            lonInput.value = pos.lng();
            geocoder.geocode({ location: pos })
                .then(res => {
                    const result = res.results[0];
                    addressInput.value = result ? result.formatted_address : "";
                    const components = result ? result.address_components : [];
                    const city = components.find(c => c.types.includes("locality"))?.long_name
                               || components.find(c => c.types.includes("administrative_area_level_2"))?.long_name
                               || "";
                    cityInput.value = city;        // para mostrar al usuario
document.getElementById('city-hidden').value = city; // para enviar en el formulario
                })
                .catch(e => console.log("Geocoder failed: " + e));
        });
    }

    // --- Amenidades y label de precio ---
    document.addEventListener('DOMContentLoaded', function() {
        const listingTypeRadios = document.querySelectorAll('input[name="listing_type"]');
        const amenityCategories = document.querySelectorAll('.amenity-category');
        const priceLabel = document.getElementById('price-label');

        function toggleAmenities() {
            const selectedType = document.querySelector('input[name="listing_type"]:checked').value;
            amenityCategories.forEach(category => {
                category.style.display = (category.dataset.type === selectedType) ? 'block' : 'none';
            });
        }

        listingTypeRadios.forEach(r => r.addEventListener('change', toggleAmenities));
        toggleAmenities();

        function updatePriceLabel() {
            const selectedType = document.querySelector('input[name="listing_type"]:checked').value;
            priceLabel.textContent = selectedType === 'rent' ? 'Precio por día (MXN)' : 'Precio de venta (MXN)';
        }

        listingTypeRadios.forEach(r => r.addEventListener('change', updatePriceLabel));
        updatePriceLabel();

        addressInput.addEventListener('keydown', function(e) {
            if(e.key === 'Enter') e.preventDefault();
        });
    });
</script>

</body>
</html>
