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

        /* Estilos del modal (del old, adaptados a Tailwind) */
        .modal { display: flex; justify-content:center; align-items:center; position:fixed; inset:0; background:rgba(0,0,0,0.6); transition: opacity 0.25s ease; }
        .modal.hidden { opacity: 0; pointer-events: none; }
        .modal-content { background:white; padding:1.5rem; border-radius:0.75rem; max-width:800px; width:100%; transform: scale(0.95); transition: transform 0.25s ease; }
        .modal:not(.hidden) .modal-content { transform: scale(1); } /* Animación al abrir */
        .carousel-container { display:flex; gap:0.75rem; overflow-x:auto; scroll-behavior:smooth; }
        .carousel-item { min-width:160px; height:110px; object-fit:cover; border-radius:0.5rem; }
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
        Sin beca <span class="text-gray-700"> no hay renta </span>
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

          <div id="price-slider" class="mb-4"></div>

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
      <button type="button" onclick="openLoginModal()"
        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
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

    {{-- Modal de Propiedad --}}
    <div id="propertyModal" class="modal hidden">
        <div class="modal-content">
            <button onclick="closePropertyModal()" class="absolute right-3 top-3 text-gray-500 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <div id="propertyCarousel" class="carousel-container"></div>
                </div>
                <div id="modalInfo" class="space-y-2"></div>
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


        // Función para abrir modal con info y carrusel de imágenes
        function openPropertyModal(prop) {
            carousel.innerHTML = '';
            modalInfo.innerHTML = '';

            modalInfo.innerHTML = `
                <h3 class="font-semibold text-lg truncate mb-1">${prop.title}</h3>
                <p class="text-sm text-gray-600 truncate mb-2">${prop.location ?? ''}</p>
                <p class="text-lg font-bold text-blue-600">$${Number(prop.price).toLocaleString('es-MX')}</p>
                <a href="/properties/${prop.id}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">Ver detalles</a>
            `;

            const images = prop.images && prop.images.length ? prop.images : [];
            if(images.length > 0) {
                images.forEach(imgData => {
                    const imgEl = document.createElement('img');
                    imgEl.src = `{{ asset('storage') }}/${imgData.image_path}`;
                    imgEl.alt = prop.title;
                    imgEl.className = 'carousel-item';
                    carousel.appendChild(imgEl);
                });
            } else {
                const imgEl = document.createElement('img');
                imgEl.src = 'https://via.placeholder.com/400x200?text=Sin+Imagen';
                imgEl.alt = 'Sin imagen';
                imgEl.className = 'carousel-item';
                carousel.appendChild(imgEl);
            }

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
                const vMin = Math.round(values[0]);
                const vMax = Math.round(values[1]);
                minDisplay.textContent = formatter.format(vMin);
                maxDisplay.textContent = formatter.format(vMax);
                minInput.value = vMin;
                maxInput.value = vMax;
                minPriceCompat.value = vMin;
                maxPriceCompat.value = vMax;
                updateButtonText(vMin, vMax, cfg);
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

                // 1) sincroniza inputs que usa filterMarkers()
                minPriceCompat.value = cfg.min;
                maxPriceCompat.value = cfg.max;

                // 2) reconfigura el slider a los nuevos límites
                initSlider();

                // 3) aplica el filtro inmediatamente
                if (typeof filterMarkers === 'function') filterMarkers();
            });
        }

        initSlider();
    });
    </script>
    <footer class="bg-gray-800 text-white py-8 mt-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center text-white font-bold text-xl mb-4">
          <i class="fas fa-home mr-2"></i>
          <span>SIN BECA NO HAY RENTA</span>
        </div>
        <p class="text-gray-300 mb-4">
          Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades
          con sus futuros dueños de manera eficiente y profesional.
        </p>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>

      <div>
        <h3 class="font-semibold text-lg mb-4">Navegación</h3>
        <ul class="space-y-2 text-gray-300">
          <li><a href="{{ route('visits.my') }}" class="hover:text-white transition">Mis Visitas</a></li>
          <li><a href="{{ route('properties.map') }}" class="hover:text-white transition">Mapa</a></li>
          <li><a href="{{ route('agent.home') }}" class="hover:text-white transition">Panel de Agente</a></li>
          <li><a href="{{ route('agent.view') }}" class="hover:text-white transition">Modo Vendedor</a></li>
        </ul>
      </div>

      <div>
        <h3 class="font-semibold text-lg mb-4">Contacto</h3>
        <ul class="space-y-2 text-gray-300">
          <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> soporte@sinbeca.com</li>
          <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
          <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Ciudad, País</li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-700 mt-8 pt-6">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-300 text-sm">&copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <a href="#" class="text-gray-300 hover:text-white text-sm transition">Privacidad</a>
          <a href="#" class="text-gray-300 hover:text-white text-sm transition">Términos</a>
          <a href="#" class="text-gray-300 hover:text-white text-sm transition">Cookies</a>
        </div>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
