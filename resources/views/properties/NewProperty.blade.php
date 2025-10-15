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
        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
    </div>

    <div class="mb-3">
        <label for="address-input" class="form-label">Dirección</label>
        <input type="text" class="form-control" id="address-input" name="location" placeholder="Escribe la dirección" required>
    </div>

    <div id="map"></div>

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <!-- Subida de imágenes -->
    <div class="mb-3">
        <label class="form-label">Imágenes de la Propiedad</label>
        <div>
            <label for="images" class="btn btn-secondary">Añadir Imágenes</label>
            <input type="file" id="images" name="images[]" multiple class="d-none">
        </div>

        {{-- Contenedor para la previsualización de imágenes --}}
        <div id="image-preview-container" class="mt-3 row g-3"></div>
    </div>


    <div class="mb-3">
        <h3>Amenidades</h3>
        @foreach($amenityCategories as $category)
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
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Guardar Propiedad</button>
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

  const addressInput = document.getElementById('address-input');

    addressInput.addEventListener('keydown', function(event) {
    // Verificamos si la tecla presionada es "Enter"
        if (event.key === 'Enter' || event.keyCode === 13) {
        // Prevenimos la acción por defecto (enviar el formulario)
        event.preventDefault();
        }
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
<script>
    // Referencias a los elementos del DOM
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const propertyForm = document.querySelector('form');

    // Usamos un objeto DataTransfer para almacenar los archivos seleccionados
    const fileStore = new DataTransfer();

    // Evento que se dispara cuando el usuario selecciona archivos
    imageInput.addEventListener('change', (e) => {
        // Añadimos los nuevos archivos seleccionados a nuestro almacén
        for (const file of e.target.files) {
            fileStore.items.add(file);
        }

        // Actualizamos los archivos del input original con nuestra lista curada
        imageInput.files = fileStore.files;

        // Actualizamos la vista previa
        renderPreviews();
    });

    // Función para renderizar las previsualizaciones
    function renderPreviews() {
        previewContainer.innerHTML = ''; // Limpiamos el contenedor

        // Recorremos los archivos en nuestro almacén
        Array.from(fileStore.files).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = () => {
                // Creamos el HTML para cada imagen
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'col-auto';
                previewWrapper.innerHTML = `
                    <div class="position-relative">
                        <img src="${reader.result}" class="img-thumbnail" width="150" height="150" style="object-fit: cover; width: 150px; height: 150px;">
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

    // Función para eliminar una imagen
    function removeImage(index) {
        const newFiles = new DataTransfer();
        const currentFiles = Array.from(fileStore.files);

        // Creamos una nueva lista de archivos sin el que queremos eliminar
        currentFiles.forEach((file, i) => {
            if (i !== index) {
                newFiles.items.add(file);
            }
        });

        // Actualizamos nuestro almacén y el input
        fileStore.clearData();
        for (const file of newFiles.files) {
            fileStore.items.add(file);
        }
        imageInput.files = fileStore.files;

        // Volvemos a renderizar las previsualizaciones
        renderPreviews();
    }
</script>
</body>
</html>
