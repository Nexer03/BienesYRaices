<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Propiedad</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        #map { height: 400px; width: 100%; margin-bottom: 20px; }
        /* mini estética para thumbnails */
        .thumb { object-fit: cover; width: 150px; height: 150px; }
    </style>
</head>
<body class="container mt-5">

<h1>Editar Propiedad: {{ $property->title }}</h1>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

<form id="property-edit-form"
      action="{{ route('properties.update', $property) }}"
      method="POST"
      enctype="multipart/form-data">
  @csrf
  @method('PUT')

  {{-- Título --}}
  <div class="mb-3">
    <label for="title" class="form-label">Título</label>
    <input type="text" class="form-control" id="title" name="title"
           value="{{ old('title', $property->title) }}" required>
  </div>

  {{-- Descripción --}}
  <div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $property->description) }}</textarea>
  </div>

  {{-- Tipo (si tu BD realmente tiene esta columna "type") --}}
  <div class="mb-3">
    <label for="type" class="form-label">Tipo</label>
    <select class="form-select" id="type" name="type" required>
      <option value="house"      @selected(old('type', $property->type) == 'house')>Casa</option>
      <option value="apartment"  @selected(old('type', $property->type) == 'apartment')>Departamento</option>
      <option value="land"       @selected(old('type', $property->type) == 'land')>Terreno</option>
      <option value="office"     @selected(old('type', $property->type) == 'office')>Oficina</option>
    </select>
  </div>

  {{-- Habitaciones y Baños --}}
  <div class="row mb-3">
    <div class="col-md-6">
      <label for="bedrooms" class="form-label">Habitaciones</label>
      <input type="number" min="0" class="form-control" id="bedrooms" name="bedrooms"
             value="{{ old('bedrooms', $property->bedrooms) }}">
    </div>
    <div class="col-md-6">
      <label for="bathrooms" class="form-label">Baños</label>
      <input type="number" min="0" class="form-control" id="bathrooms" name="bathrooms"
             value="{{ old('bathrooms', $property->bathrooms) }}">
    </div>
  </div>

  {{-- Propósito (Renta/Venta) --}}
  <div class="mb-3">
    <label class="form-label">Propósito</label>
    <div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="listing_type" id="rent" value="rent"
               @checked(old('listing_type', $property->listing_type) == 'rent')>
        <label class="form-check-label" for="rent">Renta</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="listing_type" id="sale" value="sale"
               @checked(old('listing_type', $property->listing_type) == 'sale')>
        <label class="form-check-label" for="sale">Venta</label>
      </div>
    </div>
  </div>

  {{-- Precio --}}
  <div class="mb-3">
    <label for="price" class="form-label" id="price-label">Precio</label>
    <input type="number" class="form-control" id="price" name="price"
           value="{{ old('price', $property->price) }}" step="100.00" required max="99999999.99">
  </div>

  {{-- Ubicación y Mapa --}}
  <div class="mb-3">
    <label for="address-input" class="form-label">Dirección</label>
    <input type="text" class="form-control" id="address-input" name="location"
           value="{{ old('location', $property->location) }}" placeholder="Escribe la dirección" required>
    <label for="city" class="form-label mt-2">Ciudad</label>
    <input type="text" name="city" id="city" value="{{ old('city', $property->city) }}" class="form-control" readonly>
  </div>

  <div id="map"></div>
  <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude', $property->latitude) }}">
  <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">

  {{-- Imágenes actuales --}}
  <div class="mb-3">
    <label class="form-label">Imágenes actuales</label>
    <div id="existing-images-container" class="row g-3">
      @forelse($property->images as $image)
        <div class="col-auto" id="image-{{ $image->id }}">
          <div class="position-relative">
            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail thumb" alt="Imagen">
            <button type="button"
                    class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image-btn"
                    data-image-id="{{ $image->id }}"
                    data-delete-url="{{ route('properties.images.destroy', $image) }}">
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
      <input type="file" id="images" name="images[]" multiple class="d-none" accept="image/*">
    </div>
    <div id="new-image-preview-container" class="mt-3 row g-3"></div>
  </div>

  {{-- Amenidades --}}
  <div class="mb-3">
    <h3>Amenidades</h3>
    @php $propertyAmenities = $property->amenities->pluck('id')->toArray(); @endphp
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
                <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                       id="amenity-{{ $amenity->id }}"
                       @checked(in_array($amenity->id, $propertyAmenities))>
                <label class="form-check-label" for="amenity-{{ $amenity->id }}">{{ $amenity->name }}</label>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  <div class="d-flex gap-2 mb-3">
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>

{{-- =================== SCRIPTS =================== --}}
<script>
  // PREVIEW de imágenes nuevas
  const newImageInput    = document.getElementById('images');
  const previewContainer = document.getElementById('new-image-preview-container');
  const fileStore        = new DataTransfer();

  if (newImageInput) {
    newImageInput.addEventListener('change', (e) => {
      for (const file of e.target.files) fileStore.items.add(file);
      newImageInput.files = fileStore.files;
      renderNewImagePreviews();
    });
  }

  function renderNewImagePreviews() {
    previewContainer.innerHTML = '';
    Array.from(fileStore.files).forEach((file, idx) => {
      const reader = new FileReader();
      reader.onload = () => {
        const wrap = document.createElement('div');
        wrap.className = 'col-auto';
        wrap.innerHTML = `
          <div class="position-relative">
            <img src="${reader.result}" class="img-thumbnail thumb">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removeNewImage(${idx})">&times;</button>
          </div>`;
        previewContainer.appendChild(wrap);
      };
      reader.readAsDataURL(file);
    });
  }
  function removeNewImage(index) {
    const kept = Array.from(fileStore.files).filter((_, i) => i !== index);
    fileStore.items.clear();
    kept.forEach(f => fileStore.items.add(f));
    newImageInput.files = fileStore.files;
    renderNewImagePreviews();
  }

  // BORRADO AJAX con SweetAlert2 (delegación robusta)
  (function () {
    const imageContainer = document.getElementById('existing-images-container');
    if (!imageContainer) return;

    imageContainer.addEventListener('click', async (ev) => {
      // Soporta clic en hijos dentro del botón
      const btn = ev.target.closest('.delete-image-btn');
      if (!btn || !imageContainer.contains(btn)) return;
      ev.preventDefault();

      // Verifica que SweetAlert2 esté disponible
      if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado en esta vista.');
        return;
      }

      const imgId     = btn.dataset.imageId;
      const deleteUrl = btn.dataset.deleteUrl;

      const result = await Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
      });

      if (!result.isConfirmed) return;

      try {
        const res = await fetch(deleteUrl, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        });

        // Si la respuesta no es JSON válido, evita fallo silencioso
        let data = {};
        try { data = await res.json(); } catch (e) {}

        if (!res.ok || !data.success) {
          throw new Error((data && data.message) || 'No se pudo eliminar la imagen.');
        }

        // Quitar del DOM con una micro animación
        const card = document.getElementById(`image-${imgId}`);
        if (card) {
          card.style.transition = 'opacity .2s ease';
          card.style.opacity = '0';
          setTimeout(() => card.remove(), 200);
        }

        Swal.fire({
          icon: 'success',
          title: 'Imagen eliminada',
          showConfirmButton: false,
          timer: 1200
        });

      } catch (e) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: e.message || 'Error eliminando imagen.',
        });
      }
    });
  })();


  // PREVENIR submit con Enter en Dirección y Precio
  const addressInput = document.getElementById('address-input');
  const priceInput   = document.getElementById('price');
  if (addressInput) addressInput.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
  if (priceInput)   priceInput.addEventListener('keydown',   e => { if (e.key === 'Enter') e.preventDefault(); });

  // Validación previa al submit (requiere coords y ciudad si hay dirección)
  const form = document.getElementById('property-edit-form');
  form.addEventListener('submit', (e) => {
    const lat  = document.getElementById('latitude').value;
    const lng  = document.getElementById('longitude').value;
    const city = document.getElementById('city').value;
    if (addressInput.value && (!city || !lat || !lng)) {
      e.preventDefault();
      alert('Selecciona una dirección de las sugerencias para completar ciudad y coordenadas.');
      addressInput.focus();
    }
  });

  // Google Maps (Autocomplete + Drag)
  fetch('/maps-key')
    .then(res => res.json())
    .then(data => {
      const s = document.createElement('script');
      s.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
      s.async = true;
      document.head.appendChild(s);
    });

  function initMap() {
    const start = {
      lat: parseFloat(document.getElementById('latitude').value)  || 20.749757,
      lng: parseFloat(document.getElementById('longitude').value) || -105.258849
    };

    const map = new google.maps.Map(document.getElementById("map"), { center: start, zoom: 16 });
    const marker = new google.maps.Marker({ map, draggable: true, position: start });
    const geocoder = new google.maps.Geocoder();

    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const cityInput= document.getElementById('city');

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

      let city = "";
      const comps = place.address_components || [];
      for (const c of comps) {
        if (c.types.includes("locality") || c.types.includes("administrative_area_level_2")) { city = c.long_name; break; }
      }
      cityInput.value = city;
    });

    marker.addListener('dragend', function() {
      const pos = marker.getPosition();
      latInput.value = pos.lat();
      lonInput.value = pos.lng();
      geocoder.geocode({ location: pos }).then((res) => {
        const r = res.results?.[0];
        addressInput.value = r ? r.formatted_address : '';
        let city = "";
        const comps = r?.address_components || [];
        for (const c of comps) {
          if (c.types.includes("locality") || c.types.includes("administrative_area_level_2")) { city = c.long_name; break; }
        }
        cityInput.value = city;
      }).catch(console.error);
    });

    // Label dinámico del precio
    function updatePriceLabel() {
      const selectedType = document.querySelector('input[name="listing_type"]:checked')?.value;
      document.getElementById('price-label').textContent =
        selectedType === 'rent' ? 'Precio por día (MXN)' : 'Precio de venta (MXN)';
    }
    document.querySelectorAll('input[name="listing_type"]').forEach(r => r.addEventListener('change', updatePriceLabel));
    updatePriceLabel();
  }

  // Filtrado de amenidades por tipo
  (function toggleAmenities() {
    const selectedType = document.querySelector('input[name="listing_type"]:checked')?.value;
    document.querySelectorAll('.amenity-category').forEach(cat => {
      const show = cat.dataset.type === selectedType;
      cat.style.display = show ? 'block' : 'none';
      if (!show) cat.querySelectorAll('input[type="checkbox"]').forEach(ch => ch.checked = false);
    });
  })();
  document.querySelectorAll('input[name="listing_type"]').forEach(r => r.addEventListener('change', () => {
    const selectedType = document.querySelector('input[name="listing_type"]:checked')?.value;
    document.querySelectorAll('.amenity-category').forEach(cat => {
      const show = cat.dataset.type === selectedType;
      cat.style.display = show ? 'block' : 'none';
      if (!show) cat.querySelectorAll('input[type="checkbox"]').forEach(ch => ch.checked = false);
    });
  }));

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
