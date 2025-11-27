<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Crear Nueva Propiedad</title>

  {{-- Anti-flash: aplicar tema guardado antes de Tailwind --}}
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
  <script> tailwind.config = { darkMode: 'class' }; </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* Transiciones del wizard */
    .step-panel { transition: transform .35s ease, opacity .35s ease; }
    .step-hidden { transform: translateX(4rem); opacity: 0; pointer-events: none; }
    .step-active { transform: translateX(0); opacity: 1; }

    /* Mapa */
    #map { height: 360px; width: 100%; border-radius: .75rem; position: relative; }
    /* Capa de fade para transición suave del estilo del mapa */
    #map .map-style-fader{
      position:absolute; inset:0;
      background:#0b1220; /* un tono oscuro que combina con dark */
      opacity:0; pointer-events:none;
      transition: opacity .25s ease;
      border-radius: inherit;
    }

    /* 🔹 Estilo moderno para campos del formulario (modo claro) */
    input[type="text"],
    input[type="number"],
    input[type="file"],
    textarea,
    select {
      background-color: #fff;
      border: 1.5px solid #d1d5db;
      border-radius: 0.75rem;
      padding: 0.6rem 0.9rem;
      transition: all 0.25s ease;
      width: 100%;
    }
    input[type="text"]:hover,
    input[type="number"]:hover,
    textarea:hover,
    select:hover { border-color: #9ca3af; }
    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus,
    select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
      outline: none;
    }
    input[readonly] { background-color: #f3f4f6; border-color: #d1d5db; color:#6b7280; }
    input[type="radio"], input[type="checkbox"] { accent-color:#3b82f6; transform: scale(1.15); }
    ::placeholder { color:#9ca3af; }

    /* 🔹 Overrides para modo oscuro */
    html.dark input[type="text"],
    html.dark input[type="number"],
    html.dark input[type="file"],
    html.dark textarea,
    html.dark select {
      background-color: #020617; /* slate-950 */
      border-color: #374151;     /* gray-700 */
      color: #e5e7eb;            /* gray-200 */
    }
    html.dark input[readonly] {
      background-color: #020617;
      border-color: #4b5563;
      color: #9ca3af;
    }
    html.dark ::placeholder { color:#6b7280; }

    html.dark #map { background-color: #020617; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col
             dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">
  <!-- Header -->
  <x-main-header />

  {{-- Botón flotante para tema (igual que en editar propiedad) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <!-- Contenido -->
  <main class="flex-1">
    <div class="max-w-5xl mx-auto px-6 py-10">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Crear Nueva Propiedad</h1>
        <p class="text-gray-600 dark:text-gray-300">Completa los pasos y guarda tu propiedad.</p>
      </div>

      <!-- Wizard -->
      <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 transition-colors">
        <!-- Progress -->
        <div class="px-6 pt-6">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                <div id="progressBar" class="h-2 rounded-full bg-blue-500" style="width:20%"></div>
              </div>
            </div>
            <div class="ml-4 text-sm text-gray-600 dark:text-gray-300">
              <span id="stepLabel">Paso 1 de 5</span>
            </div>
          </div>
          <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>General</span><span>Comercial</span><span>Ubicación</span><span>Amenidades</span><span>Confirmación</span>
          </div>
        </div>

        <form id="property-create-form"
              action="{{ route('properties.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="px-6 pb-6">
          @csrf

          <!-- PANELES -->
          <div class="relative overflow-hidden mt-8 min-h-[420px]">
            <!-- 1. General -->
            <section class="step-panel step-active" data-step="1">
              <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título de la propiedad</label>
                  <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="md:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
                  <textarea id="description" name="description" rows="4" placeholder="Describe la propiedad, zona, reglas, etc."></textarea>
                </div>

                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tipo</label>
                  <select id="type" name="type" required>
                    <option value="">Selecciona…</option>
                    <option value="house">Casa</option>
                    <option value="apartment">Departamento</option>
                    <option value="land">Terreno</option>
                    <option value="office">Oficina</option>
                  </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Habitaciones</label>
                    <input type="number" min="1" id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}" required>
                  </div>
                  <div>
                    <label for="bathrooms" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Baños</label>
                    <input type="number" min="1" id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}" required>
                  </div>
                </div>
              </div>
            </section>

            <!-- 2. Comercial -->
            <section class="step-panel step-hidden absolute inset-0" data-step="2">
              <div class="grid md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <span class="block text-sm font-medium text-gray-700 dark:text-gray-200">Propósito</span>
                  <div class="mt-2 flex items-center gap-6 text-gray-800 dark:text-gray-100">
                    <label class="inline-flex items-center gap-2">
                      <input class="text-blue-600 border-gray-300 focus:ring-blue-500" type="radio" name="listing_type" id="rent" value="rent" checked>
                      <span>Renta</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                      <input class="text-blue-600 border-gray-300 focus:ring-blue-500" type="radio" name="listing_type" id="sale" value="sale">
                      <span>Venta</span>
                    </label>
                  </div>
                </div>

                <div class="md:col-span-2">
                  <label for="price" id="price-label" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Precio por día (MXN)</label>
                  <div class="mt-1 relative">
                    <span class="absolute left-1 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                    <input type="number" id="price" name="price" step="any" required max="99999999.99" class="pl-7">
                  </div>
                </div>

                <!-- Imágenes -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Imágenes de la Propiedad</label>
                  <div class="mt-2 flex items-center gap-3 flex-wrap">
                    <label for="images" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-gray-800 dark:text-gray-100">
                      <i class="fa-solid fa-camera"></i> Añadir imágenes
                    </label>
                    <input type="file" id="images" name="images[]" multiple class="hidden" accept="image/*">
                    <span id="images-counter" class="text-xs text-gray-600 dark:text-gray-400">
                      0 seleccionadas · mínimo 5 para continuar
                    </span>
                  </div>
                  <div id="image-preview-container" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                </div>
              </div>
            </section>

            <!-- 3. Ubicación -->
            <section class="step-panel step-hidden absolute inset-0" data-step="3">
              <div class="space-y-6">
                <div>
                  <label for="address-input" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Dirección</label>
                  <input type="text" id="address-input" name="location" placeholder="Calle, número, ciudad" value="{{ old('location') }}" autocomplete="off" required>
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecciona una sugerencia para autocompletar ciudad y coordenadas.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                  <div class="md:col-span-2">
                    <div id="map" class="bg-gray-100 dark:bg-gray-800 rounded-xl"></div>
                  </div>
                  <div class="space-y-4">
                    <div>
                      <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ciudad</label>
                      <input type="text" id="city" class="mt-1 w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" readonly placeholder="Se rellenará automáticamente">
                      <input type="hidden" name="city" id="city-hidden">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Latitud</label>
                        <input type="text" id="latitude" name="latitude" class="mt-1 w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" readonly>
                      </div>
                      <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400">Longitud</label>
                        <input type="text" id="longitude" name="longitude" class="mt-1 w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- 4. Amenidades -->
            <section class="step-panel step-hidden absolute inset-0" data-step="4">
              <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Amenidades</h3>
                @foreach($amenityCategories as $category)
                  @php
                    $saleCategories = ['Cocina y Electrodomésticos','Exterior y Lote','Características Interiores','Servicios y Seguridad'];
                    $type = in_array($category->name, $saleCategories) ? 'sale' : 'rent';
                  @endphp
                  <div class="amenity-category rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900" data-type="{{ $type }}">
                    <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">{{ $category->name }}</h4>
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                      @foreach($category->amenities as $amenity)
                        <label class="inline-flex items-center gap-2">
                          <input class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500"
                                 type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity-{{ $amenity->id }}">
                          <span class="text-gray-700 dark:text-gray-200">{{ $amenity->name }}</span>
                        </label>
                      @endforeach
                    </div>
                  </div>
                @endforeach
                <p class="text-xs text-gray-500 dark:text-gray-400">Selecciona al menos 5 amenidades para continuar.</p>
              </div>
            </section>

            <!-- 5. Confirmación -->
            <section class="step-panel step-hidden absolute inset-0" data-step="5">
              <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Revisión y envío</h3>
                <p class="text-gray-600 dark:text-gray-300">Verifica que la información sea correcta. Puedes regresar a cualquier paso para editar.</p>

                <div class="grid md:grid-cols-2 gap-6">
                  <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                    <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Información general</h4>
                    <ul class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                      <li><span class="text-gray-500 dark:text-gray-400">Título:</span> <span id="rev-title">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Tipo:</span> <span id="rev-type">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Habitaciones:</span> <span id="rev-bed">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Baños:</span> <span id="rev-bath">—</span></li>
                    </ul>
                  </div>
                  <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                    <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Comercial</h4>
                    <ul class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                      <li><span class="text-gray-500 dark:text-gray-400">Propósito:</span> <span id="rev-purpose">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Precio:</span> $<span id="rev-price">—</span> MXN</li>
                    </ul>
                  </div>
                  <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 md:col-span-2 bg-white dark:bg-gray-900">
                    <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Ubicación</h4>
                    <ul class="text-sm text-gray-700 dark:text-gray-200 space-y-1">
                      <li><span class="text-gray-500 dark:text-gray-400">Dirección:</span> <span id="rev-address">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Ciudad:</span> <span id="rev-city">—</span></li>
                      <li><span class="text-gray-500 dark:text-gray-400">Coords:</span> <span id="rev-lat">—</span>, <span id="rev-lng">—</span></li>
                    </ul>
                  </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900">
                  <h4 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Imágenes cargadas</h4>
                  <div id="rev-images" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span>Se mostrarán miniaturas si agregaste imágenes.</span>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- Controles -->
          <div class="mt-8 flex items-center justify-between">
            <a href="{{ url()->previous() }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 inline-flex items-center gap-2">
              <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>

            <div class="flex items-center gap-3">
              <button type="button" id="prevBtn"
                      class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 hidden">
                Anterior
              </button>
              <button type="button" id="nextBtn"
                      class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Siguiente
              </button>
              <button type="submit" id="submitBtn"
                      class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 hidden">
                Guardar Propiedad
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <x-main-footer />

  <!-- ============== MODAL GENÉRICO ============== -->
  <div id="app-modal" class="fixed inset-0 bg-black/50 dark:bg-black/60 hidden items-center justify-center z-[100] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md p-6 relative text-gray-900 dark:text-gray-100">
      <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeAppModal()">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
      <div class="flex items-center gap-3 mb-3">
        <div id="app-modal-icon" class="text-blue-600"><i class="fa-solid fa-circle-info text-xl"></i></div>
        <h3 id="app-modal-title" class="text-lg font-semibold">Aviso</h3>
      </div>
      <div id="app-modal-message" class="text-gray-700 dark:text-gray-200"></div>
      <div class="mt-5 flex justify-end gap-2">
        <button type="button" class="px-4 py-2 rounded-lg border text-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800" onclick="closeAppModal()">Cerrar</button>
        <button type="button" id="app-modal-confirm" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Entendido</button>
      </div>
    </div>
  </div>

  <!-- JS: Menú móvil (si tu header lo usa) -->
  <script>
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    menuToggle?.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
  </script>

  <!-- JS: Utilidades modal -->
  <script>
    const appModal = document.getElementById('app-modal');
    const appModalTitle = document.getElementById('app-modal-title');
    const appModalMsg = document.getElementById('app-modal-message');
    const appModalConfirm = document.getElementById('app-modal-confirm');
    const appModalIcon = document.getElementById('app-modal-icon');

    function openAppModal({ title = 'Aviso', message = '', type = 'info', onConfirm = null, confirmText = 'Entendido' } = {}) {
      appModalTitle.textContent = title;
      appModalMsg.innerHTML = message;
      appModalConfirm.textContent = confirmText;

      let iconHtml = '<i class="fa-solid fa-circle-info text-xl"></i>';
      if (type === 'error') iconHtml = '<i class="fa-solid fa-triangle-exclamation text-xl text-red-600"></i>';
      if (type === 'success') iconHtml = '<i class="fa-solid fa-circle-check text-xl text-green-600"></i>';
      if (type === 'warning') iconHtml = '<i class="fa-solid fa-circle-exclamation text-xl text-yellow-500"></i>';
      appModalIcon.innerHTML = iconHtml;

      const handler = () => { closeAppModal(); if (typeof onConfirm === 'function') onConfirm(); };
      appModalConfirm.onclick = handler;

      appModal.classList.remove('hidden'); appModal.classList.add('flex');
    }
    function closeAppModal() { appModal.classList.add('hidden'); appModal.classList.remove('flex'); }
    window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAppModal(); });
    appModal.addEventListener('click', (e) => { if (e.target === appModal) closeAppModal(); });
  </script>

  <!-- JS: Wizard (con validaciones por paso) -->
  <script>
    const panels = Array.from(document.querySelectorAll('.step-panel'));
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');
    const stepLabel = document.getElementById('stepLabel');
    const form = document.getElementById('property-create-form');

    // refs campos
    const f = {
      title: document.getElementById('title'),
      description: document.getElementById('description'),
      type: document.getElementById('type'),
      bedrooms: document.getElementById('bedrooms'),
      bathrooms: document.getElementById('bathrooms'),
      price: document.getElementById('price'),
      address: document.getElementById('address-input'),
      lat: document.getElementById('latitude'),
      lng: document.getElementById('longitude'),
      cityHidden: document.getElementById('city-hidden'),
    };

    let currentStep = 1;
    const totalSteps = panels.length;

    function showStep(n) {
      panels.forEach(p => {
        const step = Number(p.dataset.step);
        if (step === n) { p.classList.remove('step-hidden','absolute'); p.classList.add('step-active'); }
        else { p.classList.add('step-hidden','absolute'); p.classList.remove('step-active'); }
      });
      prevBtn.classList.toggle('hidden', n === 1);
      nextBtn.classList.toggle('hidden', n === totalSteps);
      submitBtn.classList.toggle('hidden', n !== totalSteps);

      const pct = Math.max(20, Math.min(100, Math.round((n/totalSteps)*100)));
      progressBar.style.width = pct + '%';
      stepLabel.textContent = `Paso ${n} de ${totalSteps}`;

      if (n === totalSteps) fillReview();
    }

    function countSelectedAmenities() {
      return document.querySelectorAll('input[name="amenities[]"]:checked').length;
    }

    function validateStepGate(fromStep) {
      if (fromStep === 1) {
        if (!f.title.value.trim()) {
          openAppModal({ title:'Falta información', message:'Ingresa un <b>título</b> para la propiedad.', type:'warning' }); return false;
        }
        if (!f.description.value.trim()) {
          openAppModal({ title:'Falta información', message:'Añade una <b>descripción</b>.', type:'warning' }); return false;
        }
        if (!f.type.value) {
          openAppModal({ title:'Falta información', message:'Selecciona el <b>tipo de propiedad</b>.', type:'warning' }); return false;
        }
        const bedRaw  = f.bedrooms.value.trim();
        const bathRaw = f.bathrooms.value.trim();

        if (bedRaw === '' || !/^\d+$/.test(bedRaw)) {
          openAppModal({ title:'Dato requerido', message:'Indica la cantidad de <b>habitaciones</b>.', type:'warning' }); return false;
        }
        if (bathRaw === '' || !/^\d+$/.test(bathRaw)) {
          openAppModal({ title:'Dato requerido', message:'Indica la cantidad de <b>baños</b>.', type:'warning' }); return false;
        }
      }
      if (fromStep === 2) {
        if (!f.price.value || Number(f.price.value) <= 0) {
          openAppModal({ title:'Falta información', message:'Indica un <b>precio</b> válido.', type:'warning' }); return false;
        }
        if (imageStore.files.length < 5) {
          openAppModal({
            title:'Faltan imágenes',
            message:`Debes añadir al menos <b>5 imágenes</b> para continuar. Actualmente tienes <b>${imageStore.files.length}</b>.`,
            type:'warning'
          });
          return false;
        }
      }
      if (fromStep === 3) {
        if (!f.address.value.trim()) {
          openAppModal({ title:'Ubicación incompleta', message:'Selecciona una <b>dirección</b> de las sugerencias.', type:'warning' }); return false;
        }
        if (!f.lat.value || !f.lng.value || !f.cityHidden.value) {
          openAppModal({
            title:'Ubicación incompleta',
            message:'Asegúrate de elegir una sugerencia para rellenar <b>ciudad</b> y <b>coordenadas</b>.',
            type:'warning'
          });
          return false;
        }
      }
      if (fromStep === 4) {
        const amenCount = countSelectedAmenities();
        if (amenCount < 5) {
          openAppModal({
            title:'Faltan amenidades',
            message:`Selecciona al menos <b>5 amenidades</b>. Actualmente tienes <b>${amenCount}</b>.`,
            type:'warning'
          });
          return false;
        }
      }
      return true;
    }

    nextBtn.addEventListener('click', () => {
      if (!validateStepGate(currentStep)) return;
      currentStep = Math.min(totalSteps, currentStep + 1);
      showStep(currentStep);
    });

    prevBtn.addEventListener('click', () => {
      currentStep = Math.max(1, currentStep - 1);
      showStep(currentStep);
    });

    function fillReview() {
      document.getElementById('rev-title').textContent = f.title.value || '—';
      document.getElementById('rev-type').textContent = f.type.value || '—';
      document.getElementById('rev-bed').textContent = f.bedrooms.value || '0';
      document.getElementById('rev-bath').textContent = f.bathrooms.value || '0';

      const purpose = (form.querySelector('input[name="listing_type"]:checked') || {}).value || 'rent';
      document.getElementById('rev-purpose').textContent = (purpose === 'sale') ? 'Venta' : 'Renta';
      document.getElementById('rev-price').textContent = f.price.value || '0';

      document.getElementById('rev-address').textContent = f.address.value || '—';
      document.getElementById('rev-city').textContent = f.cityHidden.value || '—';
      document.getElementById('rev-lat').textContent = f.lat.value || '—';
      document.getElementById('rev-lng').textContent = f.lng.value || '—';

      const revImages = document.getElementById('rev-images');
      revImages.innerHTML = '';
      Array.from(imageStore.files).forEach(file => {
        const el = document.createElement('div');
        el.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700';
        const img = document.createElement('img');
        img.className = 'w-full h-full object-cover';
        img.src = URL.createObjectURL(file);
        el.appendChild(img);
        revImages.appendChild(el);
      });
      if (!revImages.children.length) {
        revImages.innerHTML = '<span class="text-gray-500 dark:text-gray-400 text-sm">Sin imágenes seleccionadas.</span>';
      }
    }

    showStep(currentStep);
  </script>

  <!-- JS: Imágenes (contador y eliminación) -->
  <script>
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('image-preview-container');
    const imagesCounter = document.getElementById('images-counter');
    const imageStore = new DataTransfer();

    function updateImagesCounter() {
      const n = imageStore.files.length;
      imagesCounter.textContent = `${n} seleccionadas · mínimo 5 para continuar`;
    }

    imageInput?.addEventListener('change', (e) => {
      for (const file of e.target.files) imageStore.items.add(file);
      imageInput.files = imageStore.files;
      renderPreviews();
      updateImagesCounter();
    });

    function renderPreviews() {
      previewContainer.innerHTML = '';
      Array.from(imageStore.files).forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = () => {
          const card = document.createElement('div');
          card.className = 'relative rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700';
          card.innerHTML = `
            <img src="${reader.result}" class="w-full h-32 object-cover">
            <button type="button"
              class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-7 h-7 grid place-items-center shadow"
              onclick="removeImage(${idx})">
              <i class="fa-solid fa-xmark text-sm"></i>
            </button>`;
          previewContainer.appendChild(card);
        };
        reader.readAsDataURL(file);
      });
    }

    function removeImage(index) {
      const kept = Array.from(imageStore.files).filter((_, i) => i !== index);
      imageStore.items.clear();
      kept.forEach(f => imageStore.items.add(f));
      imageInput.files = imageStore.files;
      renderPreviews();
      updateImagesCounter();
    }
  </script>

  <!-- JS: Google Maps + Autocomplete (con estilo claro/oscuro y fade suave) -->
  <script>
    // Estilos de mapa
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

    // Cargar API
    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
        script.async = true;
        document.head.appendChild(script);
      });

    // Referencia global del mapa para actualizar estilos al cambiar tema
    window.__newPropMap = null;

    function createMapFadeLayer() {
      const mapEl = document.getElementById('map');
      if (!mapEl) return null;
      let layer = mapEl.querySelector('.map-style-fader');
      if (!layer) {
        layer = document.createElement('div');
        layer.className = 'map-style-fader';
        mapEl.appendChild(layer);
      }
      return layer;
    }

    function triggerMapFade() {
      const layer = createMapFadeLayer();
      if (!layer) return;
      layer.style.opacity = '1';
      setTimeout(() => { layer.style.opacity = '0'; }, 220);
    }

    function initMap() {
      const defaultLocation = { lat: 20.709810580434393, lng: -105.28472026684265 };
      const isDark = document.documentElement.classList.contains('dark');

      const map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLocation, zoom: 14,
        styles: isDark ? darkMapStyle : lightMapStyle,
        mapTypeControl: false, streetViewControl: true
      });
      window.__newPropMap = map;

      const marker = new google.maps.Marker({ map: map, draggable: true, position: defaultLocation });
      const geocoder = new google.maps.Geocoder();
      const addressInput = document.getElementById("address-input");
      const cityInput = document.getElementById("city");
      const latInput = document.getElementById('latitude');
      const lonInput = document.getElementById('longitude');

      // Autocomplete
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

        const components = place.address_components || [];
        const city = components.find(c => c.types.includes("locality"))?.long_name
                  || components.find(c => c.types.includes("administrative_area_level_2"))?.long_name
                  || "";
        cityInput.value = city;
        document.getElementById('city-hidden').value = city;
      });

      // Drag del marcador
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
            cityInput.value = city;
            document.getElementById('city-hidden').value = city;
          })
          .catch(e => console.log("Geocoder failed:", e));
      });

      // Evitar submit con Enter si no hay sugerencias abiertas
      addressInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          const container = document.querySelector('.pac-container');
          const open = container && container.offsetParent !== null && container.querySelector('.pac-item');
          if (!open) e.preventDefault();
        }
      });
    }
    window.initMap = initMap;
  </script>

  <!-- JS: Amenidades por tipo + etiqueta de precio + validación final submit -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const listingTypeRadios = document.querySelectorAll('input[name="listing_type"]');
      const amenityCategories = document.querySelectorAll('.amenity-category');
      const priceLabel = document.getElementById('price-label');

      function toggleAmenities() {
        const selectedType = (document.querySelector('input[name="listing_type"]:checked') || {}).value || 'rent';
        amenityCategories.forEach(cat => { cat.style.display = (cat.dataset.type === selectedType) ? 'block' : 'none'; });
      }
      function updatePriceLabel() {
        const selectedType = (document.querySelector('input[name="listing_type"]:checked') || {}).value || 'rent';
        priceLabel.textContent = selectedType === 'sale' ? 'Precio de venta (MXN)' : 'Precio por día (MXN)';
      }
      listingTypeRadios.forEach(r => r.addEventListener('change', () => { toggleAmenities(); updatePriceLabel(); }));
      toggleAmenities(); updatePriceLabel();

      const priceInput = document.getElementById('price');
      const addressInput = document.getElementById('address-input');
      priceInput?.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
      addressInput?.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });

      // Validación redundante al submit (por seguridad)
      const form = document.getElementById('property-create-form');
      form?.addEventListener('submit', function (e) {
        if (!document.getElementById('title').value.trim()
          || !document.getElementById('description').value.trim()
          || !document.getElementById('type').value
          || !(Number(document.getElementById('bedrooms').value) >= 0)
          || !(Number(document.getElementById('bathrooms').value) >= 0)) {
          e.preventDefault();
          openAppModal({ title:'Falta información', type:'warning', message:'Completa todos los campos de <b>General</b>.' });
          return;
        }
        if (!priceInput.value || Number(priceInput.value) <= 0 || imageStore.files.length < 5) {
          e.preventDefault();
          openAppModal({ title:'Revisa Comercial', type:'warning', message:'Indica un <b>precio válido</b> y añade <b>al menos 5 imágenes</b>.' });
          return;
        }
        const lat = document.getElementById('latitude')?.value;
        const lon = document.getElementById('longitude')?.value;
        const city = document.getElementById('city-hidden')?.value;
        if (!addressInput.value.trim() || !lat || !lon || !city) {
          e.preventDefault();
          openAppModal({ title:'Ubicación incompleta', type:'warning', message:'Selecciona una <b>dirección</b> de las sugerencias para completar <b>ciudad</b> y <b>coordenadas</b>.' });
          return;
        }
        if (document.querySelectorAll('input[name="amenities[]"]:checked').length < 5) {
          e.preventDefault();
          openAppModal({ title:'Faltan amenidades', type:'warning', message:'Selecciona al menos <b>5 amenidades</b>.' });
          return;
        }
      });
    });
  </script>

  <!-- SCRIPT responsive del header (si aplica en tu proyecto) -->
  <script>
    const menuToggle2 = document.getElementById('menu-toggle');
    const closeMenu = document.getElementById('close-menu');
    const mainNav = document.getElementById('main-nav');
    menuToggle2?.addEventListener('click', () => { mainNav.classList.remove('translate-x-full', 'hidden'); });
    closeMenu?.addEventListener('click', () => {
      mainNav.classList.add('translate-x-full');
      setTimeout(() => mainNav.classList.add('hidden'), 300);
    });
  </script>

  <!-- Auto-open modals para mensajes del servidor -->
  <script>
    @if(session('success'))
      openAppModal({ title: 'Listo', type: 'success', message: `{!! addslashes(session('success')) !!}` });
    @endif
    @if ($errors->any())
      openAppModal({
        title: 'Revisa los datos',
        type: 'error',
        message: `<ul class="list-disc pl-5 mt-1 text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>`
      });
    @endif
  </script>

  {{-- Toggle de tema (sincronizado con localStorage) + actualización de estilo del mapa con fade --}}
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

        // Si el mapa ya está cargado, actualizamos su estilo y hacemos fade
        if (window.__newPropMap && window.google) {
          try {
            // pequeña capa para suavizar el cambio de tiles
            (function triggerMapFade(){
              const mapEl = document.getElementById('map');
              if (!mapEl) return;
              let layer = mapEl.querySelector('.map-style-fader');
              if (!layer) {
                layer = document.createElement('div');
                layer.className = 'map-style-fader';
                mapEl.appendChild(layer);
              }
              layer.style.opacity = '1';
              setTimeout(()=>{ layer.style.opacity = '0'; }, 220);
            })();

            window.__newPropMap.setOptions({ styles: isDark ? darkMapStyle : lightMapStyle });
          } catch(e) {}
        }
      }

      // Inicializar según clase actual (puesta por el script del <head>)
      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>
</body>
</html>
