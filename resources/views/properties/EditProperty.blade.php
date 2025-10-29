<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Propiedad</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        #map { height: 400px; width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body class="container mt-5">

<h1>Editar Propiedad: {{ $property->title }}</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- MUY IMPORTANTE: Le dice a Laravel que es una actualización --}}

    {{-- Título --}}
    <div class="mb-3">
        <label for="title" class="form-label">Título</label>
        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $property->title) }}" required>
    </div>

    {{-- Descripción --}}
    <div class="mb-3">
        <label for="description" class="form-label">Descripción</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $property->description) }}</textarea>
    </div>

    {{-- Tipo --}}
    <div class="mb-3">
        <label for="type" class="form-label">Tipo</label>
        <select class="form-select" id="type" name="type" required>
            <option value="house" @selected(old('type', $property->type) == 'house')>Casa</option>
            <option value="apartment" @selected(old('type', $property->type) == 'apartment')>Departamento</option>
            <option value="land" @selected(old('type', $property->type) == 'land')>Terreno</option>
            <option value="office" @selected(old('type', $property->type) == 'office')>Oficina</option>
        </select>
    </div>

    {{-- Habitaciones y Baños --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="bedrooms" class="form-label">Habitaciones</label>
            <input type="number" min="0" class="form-control" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}">
        </div>
        <div class="col-md-6">
            <label for="bathrooms" class="form-label">Baños</label>
            <input type="number" min="0" class="form-control" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}">
        </div>
    </div>

    {{-- Propósito (Renta/Venta) --}}
    <div class="mb-3">
        <label class="form-label">Propósito</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="listing_type" id="rent" value="rent" @checked(old('listing_type', $property->listing_type) == 'rent')>
                <label class="form-check-label" for="rent">Renta</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="listing_type" id="sale" value="sale" @checked(old('listing_type', $property->listing_type) == 'sale')>
                <label class="form-check-label" for="sale">Venta</label>
            </div>
        </div>
    </div>

    {{-- Precio --}}
    <div class="mb-3">
    <label for="price" class="form-label" id="price-label">Precio</label>
        <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $property->price) }}" step="0.01" required max="99999999.99">
    </div>


    {{-- Ubicación y Mapa --}}
    <div class="mb-3">
        <label for="address-input" class="form-label">Dirección</label>
        <input type="text" class="form-control" id="address-input" name="location" value="{{ old('location', $property->location) }}" placeholder="Escribe la dirección" required>
        <label for="city" class="form-label mt-2">Ciudad</label>
        <input type="text" name="city" id="city" value="{{ old('city') }}" class="form-control" readonly>
    </div>
    <div id="map"></div>
    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">
    
    <div class="mb-3">
        <label class="form-label">Imágenes Actuales</label>
        <div id="existing-images-container" class="row g-3">
            @forelse($property->images as $image)
                <div class="col-auto" id="image-{{ $image->id }}">
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail" style="object-fit: cover; width: 150px; height: 150px;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn" data-image-id="{{ $image->id }}" data-delete-url="{{ route('properties.images.destroy', $image) }}">
                            &times;
                        </button>
                    </div>
                </div>
            @empty
                <p id="no-images-message" class="col-12">No hay imágenes actuales.</p>
            @endforelse
        </div>
    </div>

    {{-- Añadir más imágenes --}}
    <div class="mb-3">
        <label class="form-label">Añadir más imágenes</label>
        <div>
            <label for="images" class="btn btn-secondary">Seleccionar Imágenes</label>
            <input type="file" id="images" name="images[]" multiple class="d-none">
        </div>
        {{-- Contenedor para la previsualización de imágenes NUEVAS --}}
        <div id="new-image-preview-container" class="mt-3 row g-3"></div>
    </div>

    {{-- Amenidades --}}
    <div class="mb-3">
        <h3>Amenidades</h3>
        @php
            $propertyAmenities = $property->amenities->pluck('id')->toArray();
        @endphp
        @foreach($amenityCategories as $category)
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
                                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}" @checked(in_array($amenity->id, $propertyAmenities))>
                                <label class="form-check-label" for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Actualizar Propiedad</button>
</form>

{{-- =================== SCRIPT UNIFICADO Y COMPLETO =================== --}}
<script>
    // --- LÓGICA DE PREVISUALIZACIÓN DE IMÁGENES NUEVAS ---
    const newImageInput = document.getElementById('images');
    const previewContainer = document.getElementById('new-image-preview-container'); // Necesitas un div con este ID para las nuevas imágenes
    const fileStore = new DataTransfer();

    newImageInput.addEventListener('change', (e) => {
        for (const file of e.target.files) {
            fileStore.items.add(file);
        }
        newImageInput.files = fileStore.files;
        renderNewImagePreviews();
    });

    function renderNewImagePreviews() {
        previewContainer.innerHTML = '';
        Array.from(fileStore.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = () => {
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'col-auto';
                previewWrapper.innerHTML = `
                    <div class="position-relative">
                        <img src="${reader.result}" class="img-thumbnail" style="object-fit: cover; width: 150px; height: 150px;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removeNewImage(${index})">
                            &times;
                        </button>
                    </div>
                `;
                previewContainer.appendChild(previewWrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeNewImage(index) {
        const keptFiles = Array.from(fileStore.files).filter((_, i) => i !== index);
        fileStore.items.clear();
        keptFiles.forEach(file => fileStore.items.add(file));
        newImageInput.files = fileStore.files;
        renderNewImagePreviews();
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
    // Obtenemos la ubicación actual de la propiedad 
    const propertyLocation = {
        lat: {{ old('latitude', $property->latitude) }},
        lng: {{ old('longitude', $property->longitude) }}
    };

    // Se crea el mapa centrado en la ubicación 
    const map = new google.maps.Map(document.getElementById("map"), { center: propertyLocation, zoom: 16 });

    // --- ¡LÍNEA CLAVE! Esta línea crea el marcador rojo en el mapa ---
    const marker = new google.maps.Marker({
        map: map,
        draggable: true, // Permite que el marcador se pueda arrastrar
        position: propertyLocation
    });

    // El resto de tu código para geocoder, autocomplete, etc. va aquí
    const geocoder = new google.maps.Geocoder();
    const addressInput = document.getElementById("address-input");
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');

    const autocomplete = new google.maps.places.Autocomplete(addressInput);
    autocomplete.bindTo("bounds", map);

    const cityInput = document.getElementById('city');
    autocomplete.addListener("place_changed", function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        map.setCenter(place.geometry.location);
        marker.setPosition(place.geometry.location); 
        latInput.value = place.geometry.location.lat();
        lonInput.value = place.geometry.location.lng();
         let city = "";
            if(place.address_components){
                for(const comp of place.address_components){
                    if(comp.types.includes("locality") || comp.types.includes("administrative_area_level_2")){
                        city = comp.long_name;
                        break;
                    }
                }
            }
            cityInput.value = city;
        
    });

    marker.addListener('dragend', function() {
        const pos = marker.getPosition();
        latInput.value = pos.lat();
        lonInput.value = pos.lng();
        geocoder.geocode({ location: pos })
            .then((response) => {
                if(response.results[0]){
                    addressInput.value = response.results[0].formatted_address;

                    // --- NUEVO: Rellenar ciudad ---
                    let city = "";
                    const components = response.results[0].address_components;
                    for(const comp of components){
                        if(comp.types.includes("locality") || comp.types.includes("administrative_area_level_2")){
                            city = comp.long_name;
                            break;
                        }
                    }
                    cityInput.value = city;
                } else {
                    addressInput.value = "No se pudo encontrar la dirección";
                    cityInput.value = "";
                }
            })
            .catch((e) => console.log("Geocoder failed due to: " + e));
    });
}

    // --- LÓGICA DEL FORMULARIO (SE EJECUTA CUANDO EL HTML ESTÁ LISTO) ---
    document.addEventListener('DOMContentLoaded', function() {

        // --- Lógica para filtrar amenidades ---
        const listingTypeRadios = document.querySelectorAll('input[name="listing_type"]');
        const amenityCategories = document.querySelectorAll('.amenity-category');
        function toggleAmenities() {
            const selectedType = document.querySelector('input[name="listing_type"]:checked').value;

            amenityCategories.forEach(category => {
                const categoryType = category.dataset.type;

                if (categoryType === selectedType) {
                    category.style.display = 'block';
                } else {
                    category.style.display = 'none';
                    // ¡SOLUCIÓN! Desmarcamos los checkboxes de las categorías ocultas
                    const checkboxes = category.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
        }
        listingTypeRadios.forEach(radio => radio.addEventListener('change', toggleAmenities));
        toggleAmenities();

        // --- Lógica para eliminar imágenes existentes con AJAX ---
        const imageContainer = document.getElementById('existing-images-container');
        imageContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-image-btn')) {
                event.preventDefault();
                const button = event.target;
                const imageId = button.dataset.imageId;
                const deleteUrl = button.dataset.deleteUrl;

                if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`image-${imageId}`).remove();
                        } else {
                            alert('Error al eliminar la imagen.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }
        });

        // --- Prevenir que 'Enter' envíe el formulario en el campo de dirección ---
        const addressInput = document.getElementById("address-input");
        if(addressInput) {
            addressInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.keyCode === 13) {
                    event.preventDefault();
                }
            });
        }
            const priceLabel = document.getElementById('price-label');

    function updatePriceLabel() {
        const selectedType = document.querySelector('input[name="listing_type"]:checked').value;
        priceLabel.textContent = (selectedType === 'rent') 
            ? 'Precio por día (MXN)' 
            : 'Precio de venta (MXN)';
    }

    listingTypeRadios.forEach(radio => radio.addEventListener('change', updatePriceLabel));
    updatePriceLabel(); // inicializar al cargar la página

    });
</script>

</body>
</html>
