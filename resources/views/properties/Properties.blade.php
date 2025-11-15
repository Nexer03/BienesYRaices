<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Propiedades - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mantenemos #map al 80vh para que ocupe gran parte de la pantalla */
        #map { height: 80vh; width: 100%; }

        /* Estilos para el marcador de precio (del old) */
        .price-marker {
            background-color: white;
            color: #222;
            border: 1px solid #ccc;
            border-radius: 16px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .price-marker:hover { transform: scale(1.1); }

        /* Ocultar POIs (del old) */
        .gm-style .gm-style-iw,
        .gm-style img[src*="spotlight-poi"],
        .gm-style div[style*="background-image"] {
            display: none !important;
        }

        /* ===== MODAL REDISEÑADO ===== */
        .modal {
            display: flex;
            justify-content:center;
            align-items:center;
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.55);
            transition: opacity 0.22s ease;
            z-index: 60;
        }
        .modal.hidden { opacity: 0; pointer-events: none; }

        .modal-content {
            position: relative;
            background:white;
            padding:1.5rem 1.75rem;
            border-radius:1.25rem;
            max-width:720px;
            width:100%;
            transform: translateY(8px) scale(0.97);
            transition: transform 0.22s ease;
            box-shadow: 0 20px 45px rgba(15,23,42,0.30);
        }
        .modal:not(.hidden) .modal-content {
            transform: translateY(0) scale(1);
        }

        /* Contenedor de imagen principal */
        .carousel-container {
            overflow:hidden;
            border-radius:1rem;
            background:#f3f4f6;
            aspect-ratio: 4 / 3; /* relación 4:3 */
            display:flex;
            align-items:center;
            justify-content:center;
        }

        /* Imagen dentro del modal */
        .carousel-item {
            width:100%;
            height:100%;
            object-fit:cover;
            border-radius:inherit;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col bg-gray-50">
    {{-- Encabezado --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
  <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

    <!-- Logo -->
    <div class="flex items-center space-x-3">
      <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 flex items-center">
        <i class="fas fa-home mr-2"></i>
        Sin beca&nbsp;<span class="text-gray-700">no hay renta</span>
      </a>
    </div>

    <!-- Filtros en el medio -->
    <div class="hidden md:flex items-center space-x-4 flex-1 justify-center max-w-2xl mx-8">

      <!-- Inputs ocultos (compatibilidad con filterMarkers) -->
      <input type="number" id="minPrice" class="hidden" />
      <input type="number" id="maxPrice" class="hidden" />

      <!-- Precio -->
      <div class="relative w-full max-w-md">
        <button type="button" id="price-filter-button"
          class="w-full text-left border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
          <span>Precio</span>
        </button>

        <div id="price-dropdown"
          class="hidden absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-xl z-10 p-4">
          <p class="font-semibold text-gray-800 mb-4">Rango de Precio</p>

          <div id="price-slider" class="mb-4 mx-3"></div>

          <div class="flex justify-between items-center text-sm text-gray-700">
            <div class="flex items-center gap-1 border rounded-md p-2">
              $ <span id="slider-min-value"></span>
            </div>
            <div class="text-gray-400">-</div>
            <div class="flex items-center gap-1 border rounded-md p-2">
              $ <span id="slider-max-value"></span>
            </div>
          </div>

          <input type="hidden" id="slider-min-input">
          <input type="hidden" id="slider-max-input">

          <div class="mt-4 text-right">
            <button type="button" id="apply-price-button"
              class="bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-blue-600 transition">
              Aplicar
            </button>
          </div>
        </div>
      </div>

      <!-- Tipo de propiedad -->
      <select id="listingType"
        class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400">
        <option value="rent">Renta</option>
        <option value="sale">Venta</option>
      </select>

      <!-- Botón filtrar -->
      <button id="filterBtn"
        class="bg-blue-500 text-white px-5 py-2 rounded-full hover:bg-blue-600 transition font-medium">
        Filtrar
      </button>
    </div>

    <!-- Navegación -->
    <nav class="flex items-center space-x-6 text-sm text-gray-700">
      @auth
      <a href="{{ url('/dashboard') }}"
        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition text-center">
        Perfil
      </a>
      @else
      <button type="button" onclick="openLoginModal()
        " class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
        Iniciar sesión
      </button>
      @endauth
    </nav>
  </div>
</header>


    {{-- Contenido Principal --}}
    <main class="flex-grow">
        {{-- Mapa (ocupa todo el espacio disponible) --}}
        <section class="w-full h-full">
            <div id="map"></div>
        </section>
    </main>

    {{-- Modal de Propiedad (rediseñado) --}}
    <div id="propertyModal" class="modal hidden">
        <div class="modal-content">
            {{-- Botón cerrar --}}
            <button onclick="closePropertyModal()"
                    class="absolute right-3 top-3 text-gray-400 hover:text-gray-700">
                <i class="fas fa-times text-sm"></i>
            </button>

            {{-- Corazón flotante (solo diseño, sin lógica) --}}
            <button type="button"
                    class="absolute right-12 top-3 bg-white/95 rounded-full p-2 shadow hover:bg-gray-100">
                <i class="fa-regular fa-heart text-gray-700 text-sm"></i>
            </button>

            <div class="flex flex-col md:flex-row gap-6 mt-4 md:mt-2">
                <div class="w-full md:w-1/2">
                    <div id="propertyCarousel" class="carousel-container"></div>
                </div>

                <div id="modalInfo" class="w-full md:w-1/2 flex flex-col justify-between"></div>
            </div>
        </div>
    </div>

    {{-- Script (Combinado con filtrado de marcadores) --}}
    <script>
        // Datos de propiedades pasados desde el controlador
        const properties = @json($properties);

        // Elementos del Modal
        const modal = document.getElementById('propertyModal');
        const carousel = document.getElementById('propertyCarousel');
        const modalInfo = document.getElementById('modalInfo');
        const closeModalBtn = document.querySelector('#propertyModal button');

        // Variables del mapa y marcadores
        let map;
        let markers = [];

        // Evento para cerrar modal
        if(closeModalBtn) {
            closeModalBtn.addEventListener('click', closePropertyModal);
        }
        // Cerrar al hacer clic fuera
        if(modal) {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closePropertyModal();
                }
            });
        }

        function closePropertyModal() {
            if(modal) modal.classList.add('hidden');
            if(carousel) carousel.innerHTML = ''; // Limpiar carrusel
            if(modalInfo) modalInfo.innerHTML = ''; // Limpiar info
        }

        // Carga de Google Maps API
        fetch('/maps-key')
        .then(res => res.json())
        .then(data => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
            script.async = true;
            document.head.appendChild(script);
        });

        // Inicialización del Mapa
        function initMap() {
            const defaultLocation = { lat: 20.749757, lng: -105.258849 };
            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 12,
                styles: [
                    { featureType: "poi.business", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.park", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.school", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                    { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
                ],
                mapTypeControl: false,
                streetViewControl: true
            });

            // Crear todos los marcadores y guardarlos en array
            properties.forEach(prop => {
                if (prop.latitude && prop.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(prop.latitude), lng: parseFloat(prop.longitude) },
                        map: map,
                        label: {
                            text: `$${Number(prop.price).toLocaleString('es-MX')}`,
                            className: 'price-marker'
                        },
                        icon: ' ',
                        title: prop.title,
                        listingType: prop.listing_type, // "sale" o "rent"
                        price: prop.price
                    });

                    marker.addListener('click', () => openPropertyModal(prop));

                    markers.push(marker);
                }
            });

            // Botón de filtrado
            const filterBtn = document.getElementById('filterBtn');
            if(filterBtn) filterBtn.addEventListener('click', filterMarkers);
            filterMarkers();
        }

        // Función para filtrar marcadores según precio y tipo
        function filterMarkers() {
            let minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
            let maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;

            // Evitar precios negativos
            minPrice = Math.max(0, minPrice);
            maxPrice = Math.max(0, maxPrice);

            const listingType = document.getElementById('listingType').value;

            markers.forEach(marker => {
                const matchPrice = marker.price >= minPrice && marker.price <= maxPrice;
                const matchType = listingType === '' || marker.listingType === listingType;
                marker.setMap(matchPrice && matchType ? map : null);
            });
        }

        // Función para abrir modal con info y diseño mejorado
        function openPropertyModal(prop) {
            if (!modal || !carousel || !modalInfo) return;

            carousel.innerHTML = '';
            modalInfo.innerHTML = '';

            const typeLabel    = prop.listing_type === 'sale' ? 'En venta' : 'En renta';
            const locationText = prop.location ?? '';

            // ---- LADO DERECHO: TEXTO ----
            modalInfo.innerHTML = `
                <div class="space-y-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700">
                        ${typeLabel}
                    </span>
                    <h3 class="text-xl font-semibold text-gray-900 leading-snug line-clamp-2">
                        ${prop.title}
                    </h3>
                    <p class="text-sm text-gray-600 line-clamp-2">
                        ${locationText}
                    </p>
                </div>
                <div class="mt-4 space-y-3">
                    <p class="text-2xl font-bold text-blue-600">
                        $${Number(prop.price).toLocaleString('es-MX')}
                    </p>
                    <a href="/properties/${prop.id}"
                       class="inline-flex items-center px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                        Ver detalles
                        <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            `;

            // ---- LADO IZQUIERDO: IMAGEN ----
            const images = prop.images && prop.images.length ? prop.images : [];
            let mainSrc;

            if (images.length > 0) {
                mainSrc = `{{ asset('storage') }}/${images[0].image_path}`;
            } else {
                mainSrc = 'https://via.placeholder.com/400x300?text=Sin+Imagen';
            }

            const extraCount = Math.max(0, images.length - 1);

            carousel.innerHTML = `
                <div class="relative w-full h-full">
                    <img src="${mainSrc}" alt="${prop.title}" class="carousel-item" />
                    ${
                        extraCount > 0
                        ? `<span class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] px-2 py-1 rounded-full">
                               +${extraCount} fotos
                           </span>`
                        : ''
                    }
                </div>
            `;

            modal.classList.remove('hidden');
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceSlider = document.getElementById('price-slider');
        const minInput = document.getElementById('slider-min-input');
        const maxInput = document.getElementById('slider-max-input');
        const minPriceCompat = document.getElementById('minPrice'); // kept for filterMarkers()
        const maxPriceCompat = document.getElementById('maxPrice');
        const typeSelect = document.getElementById('listingType');
        const priceButton = document.getElementById('price-filter-button');
        const priceDropdown = document.getElementById('price-dropdown');
        const applyPriceButton = document.getElementById('apply-price-button');
        const minDisplay = document.getElementById('slider-min-value');
        const maxDisplay = document.getElementById('slider-max-value');

        // Ranges (match Home defaults)
        const RANGES = {
            rent: { min: 100, max: 10000, step: 100 },
            sale: { min: 500000, max: 10000000, step: 50000 }
        };

        function getType() {
            return (typeSelect && typeSelect.value) ? typeSelect.value : '';
        }

        function getRangeByType() {
            const t = getType();
            if (t === 'sale') return RANGES.sale;
            // default rent when "Todos" or "rent"
            return RANGES.rent;
        }

        const formatter = new Intl.NumberFormat('es-MX', { style: 'decimal', maximumFractionDigits: 0 });

        function updateButtonText(minVal, maxVal, cfg) {
            const cfgMin = cfg.min, cfgMax = cfg.max;
            if (minVal > cfgMin || maxVal < cfgMax) {
                priceButton.innerHTML = `<span>$${formatter.format(minVal)} - $${formatter.format(maxVal)}</span>`;
            } else {
                priceButton.innerHTML = `<span>Precio</span>`;
            }
        }

        function initSlider() {
            if (!priceSlider) return;
            const cfg = getRangeByType();

            // Read initial from compat inputs or defaults
            let initMin = parseInt(minPriceCompat.value || cfg.min, 10);
            let initMax = parseInt(maxPriceCompat.value || cfg.max, 10);
            initMin = Math.max(cfg.min, Math.min(initMin, cfg.max));
            initMax = Math.max(cfg.min, Math.min(initMax, cfg.max));
            if (initMin > initMax) initMin = initMax;

            if (priceSlider.noUiSlider) {
                priceSlider.noUiSlider.updateOptions({
                    start: [initMin, initMax],
                    step: cfg.step,
                    range: { min: cfg.min, max: cfg.max }
                }, true);
            } else {
                noUiSlider.create(priceSlider, {
                    start: [initMin, initMax],
                    connect: true,
                    step: cfg.step,
                    range: { min: cfg.min, max: cfg.max }
                });
            }

            // Initial sync
            minInput.value = initMin;
            maxInput.value = initMax;
            minPriceCompat.value = initMin;
            maxPriceCompat.value = initMax;
            minDisplay.textContent = formatter.format(initMin);
            maxDisplay.textContent = formatter.format(initMax);
            updateButtonText(initMin, initMax, cfg);

            // Slider events
            priceSlider.noUiSlider.off && priceSlider.noUiSlider.off('update'); // ensure single binding if supported
            priceSlider.noUiSlider.on('update', function(values) {
                const minValue = Math.round(values[0]);
                const maxValue = Math.round(values[1]);
                minDisplay.textContent = formatter.format(minValue);
                maxDisplay.textContent = formatter.format(maxValue);
                minInput.value = minValue;
                maxInput.value = maxValue;
                minPriceCompat.value = minValue;
                maxPriceCompat.value = maxValue;
                updateButtonText(minValue, maxValue, cfg);
            });
        }

        // Dropdown behavior
        if (priceButton && priceDropdown) {
            priceButton.addEventListener('click', (e) => { e.stopPropagation(); priceDropdown.classList.toggle('hidden'); });
            applyPriceButton && applyPriceButton.addEventListener('click', () => {
                priceDropdown.classList.add('hidden');
                if (typeof filterMarkers === 'function') filterMarkers();
            });
            window.addEventListener('click', (e) => {
                if (!priceDropdown.classList.contains('hidden') && !priceDropdown.contains(e.target) && e.target !== priceButton) {
                    priceDropdown.classList.add('hidden');
                }
            });
        }

        // Reconfigure slider when listing type changes
        if (typeSelect) {
            typeSelect.addEventListener('change', () => {
                const cfg = getRangeByType();

                // sincroniza inputs que usa filterMarkers()
                minPriceCompat.value = cfg.min;
                maxPriceCompat.value = cfg.max;

                initSlider();

                if (typeof filterMarkers === 'function') filterMarkers();
            });
        }

        initSlider();
    });
    </script>
      <!-- FOOTER -->
  <x-main-footer />

</body>
</html>
