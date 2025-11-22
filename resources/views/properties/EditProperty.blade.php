<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Propiedad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Anti-flash: aplica el tema guardado ANTES de cargar Tailwind --}}
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

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>

  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    #map { height: 380px; width: 100%; border-radius: .75rem; }
    .dark footer { background-color: #111827 !important; }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 text-gray-800 flex flex-col
             dark:from-gray-950 dark:via-gray-900 dark:to-gray-900 dark:text-gray-100 transition-colors duration-300">
  <x-main-header />

  {{-- Botón Tema --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <main class="flex-1 max-w-6xl mx-auto px-6 py-10">
    <form id="property-edit-form"
          action="{{ route('properties.update', $property) }}"
          method="POST" enctype="multipart/form-data"
          x-data="wizard()">
      @csrf
      @method('PUT')

      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 dark:text-gray-100">Editar Propiedad</h1>
        <p class="text-gray-500 dark:text-gray-300">Actualiza información, ubicación, imágenes y amenidades en dos pasos.</p>
      </header>

      {{-- STEPPER --}}
      <div class="flex items-center justify-center mb-8 space-x-8 select-none">
        <div class="flex flex-col items-center">
          <div
            :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
            class="w-9 h-9 rounded-full flex items-center justify-center font-bold transition-all">
            1
          </div>
          <span class="text-sm mt-2 font-medium text-gray-600 dark:text-gray-300">Información</span>
        </div>
        <div class="w-24 h-1 bg-gray-300 dark:bg-gray-700 rounded-full">
          <div class="h-1 bg-blue-600 rounded-full transition-all duration-500"
               :style="{ width: step === 2 ? '100%' : '50%' }"></div>
        </div>
        <div class="flex flex-col items-center">
          <div
            :class="step === 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600 dark:bg-gray-700 dark:text-gray-300'"
            class="w-9 h-9 rounded-full flex items-center justify-center font-bold transition-all">
            2
          </div>
          <span class="text-sm mt-2 font-medium text-gray-600 dark:text-gray-300">Amenidades</span>
        </div>
      </div>

      <div class="relative overflow-hidden">
        {{-- PASO 1: Info, Comercial, Ubicación, Imágenes --}}
        <section x-show="step === 1"
                 x-transition:enter="transition transform duration-500"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition transform duration-500"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-full opacity-0"
                 class="bg-white/80 dark:bg-gray-900/90 backdrop-blur-md border border-gray-100 dark:border-gray-700 rounded-2xl shadow-lg p-6 md:p-8 space-y-10 transition-colors duration-300">

          {{-- Información básica --}}
          <div>
            <h2 class="text-xl font-semibold mb-5 text-gray-900 dark:text-gray-100">Información básica</h2>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título</label>
                <input type="text" name="title" value="{{ old('title', $property->title) }}"
                  class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                         bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                         focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
                <textarea name="description" rows="4"
                  class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                         bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                         focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $property->description) }}</textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tipo</label>
                <select name="type"
                  class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                         bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                         focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="house"     @selected(old('type', $property->type) == 'house')>Casa</option>
                  <option value="apartment" @selected(old('type', $property->type) == 'apartment')>Departamento</option>
                  <option value="land"      @selected(old('type', $property->type) == 'land')>Terreno</option>
                  <option value="office"    @selected(old('type', $property->type) == 'office')>Oficina</option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Habitaciones</label>
                  <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $property->bedrooms) }}"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Baños</label>
                  <input type="number" name="bathrooms" min="0" value="{{ old('bathrooms', $property->bathrooms) }}"
                    class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
              </div>
            </div>
          </div>

          {{-- Comercial --}}
          <div>
            <h2 class="text-xl font-semibold mb-5 text-gray-900 dark:text-gray-100">Comercial</h2>
            <div class="grid md:grid-cols-3 gap-6">
              <div class="md:col-span-2">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Propósito</span>
                <div class="flex items-center gap-6 text-gray-800 dark:text-gray-100">
                  <label class="flex items-center gap-2">
                    <input type="radio" name="listing_type" value="rent"
                           @checked(old('listing_type', $property->listing_type) == 'rent')
                           class="text-blue-600 border-gray-300 dark:border-gray-600"> Renta
                  </label>
                  <label class="flex items-center gap-2">
                    <input type="radio" name="listing_type" value="sale"
                           @checked(old('listing_type', $property->listing_type) == 'sale')
                           class="text-blue-600 border-gray-300 dark:border-gray-600"> Venta
                  </label>
                </div>
              </div>
              <div>
                <label id="price-label" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                  Precio
                </label>
                <div class="relative mt-1">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                  <input type="number" id="price" name="price" step="100.00" max="99999999.99"
                         value="{{ old('price', $property->price) }}"
                         class="pl-7 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                                bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                                focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
              </div>
            </div>
          </div>

          {{-- Ubicación --}}
          <div>
            <h2 class="text-xl font-semibold mb-5 text-gray-900 dark:text-gray-100">Ubicación</h2>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Dirección</label>
            <input id="address-input" type="text" name="location" value="{{ old('location', $property->location) }}"
                   class="w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                          bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            <div class="grid md:grid-cols-3 gap-4 mt-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ciudad</label>
                <input id="city" name="city" value="{{ old('city', $property->city) }}"
                       class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                              bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100" readonly>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Latitud</label>
                <input id="latitude" name="latitude" value="{{ old('latitude', $property->latitude) }}"
                       class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                              bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100" readonly>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Longitud</label>
                <input id="longitude" name="longitude" value="{{ old('longitude', $property->longitude) }}"
                       class="mt-1 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-inner px-3 py-2
                              bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100" readonly>
              </div>
            </div>

            <div class="mt-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 p-2">
              <div id="map" class="bg-gray-100 dark:bg-gray-800 rounded-lg"></div>
            </div>
          </div>

          {{-- Imágenes --}}
          <div>
            <h2 class="text-xl font-semibold mb-5 text-gray-900 dark:text-gray-100">Imágenes</h2>

            {{-- existentes --}}
            <div id="existing-images-container" class="flex flex-wrap gap-4">
              @forelse($property->images as $image)
                <div class="relative" id="image-{{ $image->id }}">
                  <img src="{{ asset('storage/' . $image->image_path) }}" class="w-36 h-36 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                  <button type="button"
                          class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 grid place-items-center delete-image-btn shadow"
                          title="Eliminar" data-image-id="{{ $image->id }}"
                          data-delete-url="{{ route('properties.images.destroy', $image) }}">
                    &times;
                  </button>
                </div>
              @empty
                <p id="no-images-message" class="text-gray-500 dark:text-gray-400">No hay imágenes actuales.</p>
              @endforelse
            </div>

            {{-- nuevas --}}
            <div class="mt-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Añadir más imágenes</label>
              <label for="images"
                     class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-lg cursor-pointer hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600">
                <i class="fa-solid fa-image"></i> Seleccionar imágenes
              </label>
              <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
              <div id="new-image-preview-container" class="mt-4 flex flex-wrap gap-4"></div>
            </div>
          </div>

          {{-- Acciones paso 1 --}}
          <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="button" @click="next()"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">
              Siguiente <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
          </div>
        </section>

        {{-- PASO 2: Amenidades --}}
        <section x-show="step === 2"
                 x-transition:enter="transition transform duration-500"
                 x-transition:enter-start="-translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition transform duration-500"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="bg-white/80 dark:bg-gray-900/90 backdrop-blur-md border border-gray-100 dark:border-gray-700 rounded-2xl shadow-lg p-6 md:p-8 transition-colors duration-300">

          <h2 class="text-xl font-semibold mb-5 text-gray-900 dark:text-gray-100">Amenidades</h2>
          @php $propertyAmenities = $property->amenities->pluck('id')->toArray(); @endphp
          @foreach($amenityCategories as $category)
            @php
              $saleCategories = ['Cocina y Electrodomésticos','Exterior y Lote','Características Interiores','Servicios y Seguridad'];
              $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
            @endphp
            <div class="mb-6 amenity-category" data-type="{{ $type }}">
              <h4 class="font-semibold mb-3 text-gray-800 dark:text-gray-100">{{ $category->name }}</h4>
              <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($category->amenities as $amenity)
                  <label class="inline-flex items-center gap-2">
                    <input class="text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500"
                           type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                           @checked(in_array($amenity->id, $propertyAmenities))>
                    <span class="text-gray-700 dark:text-gray-200">{{ $amenity->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach

          <div class="flex justify-between pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="button" @click="back()"
                    class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 shadow">
              <i class="fa-solid fa-arrow-left mr-2"></i> Volver
            </button>
            <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">
              Guardar cambios
            </button>
          </div>
        </section>
      </div>
    </form>
  </main>

  <x-main-footer />

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  /* ---------- Alpine helper ---------- */
  function wizard() {
    return {
      step: 1,
      next(){ if(this.step < 2) this.step++ },
      back(){ if(this.step > 1) this.step-- }
    }
  }

  /* ---------- Previews nuevas imágenes ---------- */
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
        wrap.className = 'relative';
        wrap.innerHTML = `
          <img src="${reader.result}" class="w-36 h-36 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
          <button type="button"
            class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-7 h-7 grid place-items-center shadow"
            title="Quitar" onclick="removeNewImage(${idx})">&times;</button>`;
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

  /* ---------- Borrado AJAX imágenes existentes (SweetAlert2) ---------- */
  (function () {
    const imageContainer = document.getElementById('existing-images-container');
    if (!imageContainer) return;

    imageContainer.addEventListener('click', async (ev) => {
      const btn = ev.target.closest('.delete-image-btn');
      if (!btn || !imageContainer.contains(btn)) return;
      ev.preventDefault();

      const imgId     = btn.dataset.imageId;
      const deleteUrl = btn.dataset.deleteUrl;

      const result = await Swal.fire({
        title: '¿Eliminar imagen?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33', cancelButtonColor: '#6b7280',
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
        let data = {};
        try { data = await res.json(); } catch {}
        if (!res.ok || !data.success) throw new Error((data && data.message) || 'No se pudo eliminar la imagen.');

        const card = document.getElementById(`image-${imgId}`);
        if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 180); }

        Swal.fire({ icon: 'success', title: 'Imagen eliminada', timer: 1200, showConfirmButton: false });
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'Error eliminando imagen.' });
      }
    });
  })();

  /* ---------- UX: bloquear Enter en Dirección/Precio ---------- */
  const addressInput = document.getElementById('address-input');
  const priceInput   = document.getElementById('price');
  if (addressInput) addressInput.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
  if (priceInput)   priceInput.addEventListener('keydown',   e => { if (e.key === 'Enter') e.preventDefault(); });

  /* ---------- Validación submit: requiere coords y ciudad si hay dirección ---------- */
  document.getElementById('property-edit-form').addEventListener('submit', (e) => {
    const lat  = document.getElementById('latitude').value;
    const lng  = document.getElementById('longitude').value;
    const city = document.getElementById('city').value;
    if (addressInput.value && (!city || !lat || !lng)) {
      e.preventDefault();
      alert('Selecciona una dirección de las sugerencias para completar ciudad y coordenadas.');
      addressInput.focus();
    }
  });

  /* ---------- Google Maps (Autocomplete + drag) ---------- */
  function updatePriceLabel() {
    const selectedType = document.querySelector('input[name="listing_type"]:checked')?.value;
    document.getElementById('price-label').textContent =
      selectedType === 'rent' ? 'Precio por día (MXN)' : 'Precio de venta (MXN)';
  }
  document.querySelectorAll('input[name="listing_type"]').forEach(r => r.addEventListener('change', updatePriceLabel));
  updatePriceLabel();

  // === Estilos del mapa (claro/oscuro) ===
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

  fetch('/maps-key')
    .then(res => res.json())
    .then(data => {
      const s = document.createElement('script');
      s.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
      s.async = true;
      document.head.appendChild(s);
    });

  window.initMap = function() {
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const cityInput= document.getElementById('city');

    const start = {
      lat: parseFloat(latInput.value)  || 20.709810580434393,
      lng: parseFloat(lonInput.value) || -105.28472026684265
    };

    const isDark = document.documentElement.classList.contains('dark');

    const map = new google.maps.Map(document.getElementById("map"), {
      center: start,
      zoom: 16,
      styles: isDark ? darkMapStyle : lightMapStyle
    });
    // Exponer para que el toggle de tema pueda actualizar estilos sin recrear el mapa
    window._editMap = map;
    window._editMapStyles = { light: lightMapStyle, dark: darkMapStyle };

    const marker = new google.maps.Marker({ map, draggable: true, position: start });
    const geocoder = new google.maps.Geocoder();

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
  };

  /* ---------- Mostrar/ocultar amenidades por propósito ---------- */
  function toggleAmenities() {
    const selectedType = document.querySelector('input[name="listing_type"]:checked')?.value;

    document.querySelectorAll('.amenity-category').forEach(cat => {
      const show = cat.dataset.type === selectedType;
      cat.style.display = show ? 'block' : 'none';
    });
  }
  toggleAmenities();
  document.querySelectorAll('input[name="listing_type"]').forEach(r => r.addEventListener('change', toggleAmenities));

  toggleAmenities();
  document.querySelectorAll('input[name="listing_type"]').forEach(r => r.addEventListener('change', toggleAmenities));
  </script>

  {{-- Toggle de tema (sincronizado con localStorage, igual que en welcome) --}}
  <script>
    (function () {
      const html  = document.documentElement;
      const btn   = document.getElementById('theme-toggle');
      const icon  = btn?.querySelector('i');
      const label = btn?.querySelector('span');

      function setIconAndLabel() {
        const isDark = html.classList.contains('dark');
        if (!icon || !label) return;
        icon.classList.remove('fa-sun', 'fa-moon');
        icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
        label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
      }

      function apply(mode) {
        const isDark = mode === 'dark';
        html.classList.toggle('dark', isDark);
        try { localStorage.setItem('theme', mode); } catch (e) {}
        setIconAndLabel();

        // Actualiza el estilo del MAPA sin recrearlo
        if (window._editMap && window._editMapStyles) {
          window._editMap.setOptions({
            styles: isDark ? window._editMapStyles.dark : window._editMapStyles.light
          });
        }
      }

      // Al cargar, usamos la clase ya puesta por el script del <head>
      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>
</body>
</html>
