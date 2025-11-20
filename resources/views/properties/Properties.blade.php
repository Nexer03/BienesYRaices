<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Propiedades - SIN BECA NO HAY RENTA</title>

    {{-- Anti-flash: aplica tema guardado ANTES de pintar la página --}}
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

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Mantenemos #map al 80vh para que ocupe gran parte de la pantalla */
        #map { height: 80vh; width: 100%; }

        /* Estilos para el marcador de precio */
        .price-marker {
            background-color: white;
            color: #111827;
            border: 1px solid #d1d5db;
            border-radius: 16px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .price-marker:hover { transform: scale(1.1); }

        .dark .price-marker {
            background-color: #020617; /* slate-950 */
            color: #e5e7eb;           /* gray-200 */
            border-color: #374151;    /* gray-700 */
            box-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        /* Ocultar POIs */
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

        .dark .modal-content {
            background: #020617;   /* slate-950 */
            color: #e5e7eb;
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
        .dark .carousel-container {
            background:#020617;
        }

        /* Imagen dentro del modal */
        .carousel-item {
            width:100%;
            height:100%;
            object-fit:cover;
            border-radius:inherit;
        }

        /* ==== Transición global de tema + animación botón ==== */
        html.theme-fade * {
            transition:
                background-color .35s ease,
                color .35s ease,
                border-color .35s ease,
                fill .35s ease;
        }

        #theme-toggle {
            transition: background-color .25s ease,
                        color .25s ease,
                        transform .25s ease,
                        box-shadow .25s ease;
        }

        #theme-toggle.theme-bounce {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,.18);
        }

        #theme-toggle-icon {
            transition: transform .35s ease, opacity .2s ease;
        }

        #theme-toggle-icon.theme-spin {
            transform: rotate(180deg);
        }

        .dark footer {
            background-color: #020617 !important;
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">
    {{-- Encabezado global --}}
    <x-main-header />

    {{-- Botón Tema (mismo estilo que en home) --}}
    <button id="theme-toggle"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                   bg-white text-gray-800 hover:bg-gray-100
                   dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
            aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    {{-- Contenido Principal --}}
    <main class="relative flex-grow">
        {{-- Filtros flotantes a la izquierda --}}
        <aside id="map-filter-panel"
               class="fixed top-24 left-[8rem] z-40 w-72 max-w-[90vw]">
            <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm shadow-xl rounded-2xl p-4 space-y-4 border border-gray-100 dark:border-gray-700">

                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <i class="fa-solid fa-filter text-blue-500"></i>
                        <span>Filtros del mapa</span>
                    </h2>
                </div>

                {{-- Inputs ocultos (compatibilidad con filterMarkers) --}}
                <input type="number" id="minPrice" class="hidden" />
                <input type="number" id="maxPrice" class="hidden" />

                {{-- Filtro de precio --}}
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">Precio</span>

                    <div class="relative w-full">
                        <button type="button" id="price-filter-button"
                                class="w-full text-left border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-gray-100">
                            <span>Precio</span>
                        </button>

                        <div id="price-dropdown"
                             class="hidden absolute top-full mt-2 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 p-4">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 mb-3 text-sm">Rango de Precio</p>

                            <div id="price-slider" class="mb-4 mx-1"></div>

                            <div class="flex justify-between items-center text-xs text-gray-700 dark:text-gray-200">
                                <div class="flex items-center gap-1 border rounded-md px-2 py-1 bg-gray-50 dark:bg-gray-800">
                                    $ <span id="slider-min-value"></span>
                                </div>
                                <div class="text-gray-400">-</div>
                                <div class="flex items-center gap-1 border rounded-md px-2 py-1 bg-gray-50 dark:bg-gray-800">
                                    $ <span id="slider-max-value"></span>
                                </div>
                            </div>

                            <input type="hidden" id="slider-min-input">
                            <input type="hidden" id="slider-max-input">

                            <div class="mt-4 text-right">
                                <button type="button" id="apply-price-button"
                                        class="bg-blue-500 text-white px-4 py-1.5 rounded-full text-xs font-semibold hover:bg-blue-600 transition">
                                    Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tipo de propiedad --}}
                <div class="space-y-1">
                    <label for="listingType" class="block text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">
                        Tipo de propiedad
                    </label>
                    <select id="listingType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-gray-100">
                        <option value="rent">Renta</option>
                        <option value="sale">Venta</option>
                    </select>
                </div>

                {{-- Botón filtrar --}}
                <button id="filterBtn"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition text-sm font-medium flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Aplicar filtros</span>
                </button>
            </div>
        </aside>

        {{-- Mapa con margen/padding lateral --}}
        <section class="w-full h-full max-w-7xl mx-auto px-4 md:px-6 pt-4 pb-8">
            <div id="map" class="rounded-2xl overflow-hidden shadow-md bg-gray-200 dark:bg-gray-900"></div>
        </section>
    </main>

    {{-- Modal de Propiedad (rediseñado) --}}
    <div id="propertyModal" class="modal hidden">
        <div class="modal-content">
            {{-- Botón cerrar --}}
            <button onclick="closePropertyModal()"
                    class="absolute right-3 top-3 text-gray-400 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-sm"></i>
            </button>

            {{-- Corazón flotante (solo diseño, sin lógica) --}}
            <button type="button"
                    class="absolute right-12 top-3 bg-white/95 dark:bg-gray-800/95 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fa-regular fa-heart text-gray-700 dark:text-gray-100 text-sm"></i>
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
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">
                        ${prop.title}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
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
            // default rent when "Todos" o "rent"
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

    <script>
      (function () {
        const searchToggle = document.getElementById('search-toggle');
        const filterPanel  = document.getElementById('map-filter-panel');
        if (!searchToggle || !filterPanel) return;

        function syncByWidth() {
          // Desktop: siempre visible
          if (window.innerWidth >= 768) {
            filterPanel.classList.remove('hidden');
          } else {
            // Móvil: se esconde por defecto
            filterPanel.classList.add('hidden');
          }
        }

        syncByWidth();

        // Click en la lupa → mostrar/ocultar filtros en móvil
        searchToggle.addEventListener('click', () => {
          if (window.innerWidth < 768) {
            filterPanel.classList.toggle('hidden');
          }
        });

        window.addEventListener('resize', syncByWidth);
      })();
    </script>

    {{-- Lógica del botón de tema (igual que en home) --}}
    <script>
        (function () {
            const html  = document.documentElement;
            const btn   = document.getElementById('theme-toggle');
            const icon  = document.getElementById('theme-toggle-icon');
            const label = btn?.querySelector('span');

            function setIconAndLabel() {
                const isDark = html.classList.contains('dark');
                if (!icon || !label) return;

                icon.classList.remove('fa-sun', 'fa-moon');
                icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
                label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
            }

            function startPageFade() {
                html.classList.add('theme-fade');
                setTimeout(() => html.classList.remove('theme-fade'), 400);
            }

            function animateButton() {
                if (!btn || !icon) return;
                btn.classList.add('theme-bounce');
                icon.classList.add('theme-spin');
                setTimeout(() => {
                    btn.classList.remove('theme-bounce');
                    icon.classList.remove('theme-spin');
                }, 350);
            }

            function apply(mode) {
                const isDark = mode === 'dark';
                startPageFade();
                html.classList.toggle('dark', isDark);
                try {
                    localStorage.setItem('theme', mode);
                } catch (e) {}
                setIconAndLabel();
                animateButton();
            }

            // Estado inicial de icono/texto según clase actual del <html>
            setIconAndLabel();

            btn?.addEventListener('click', () => {
                const next = html.classList.contains('dark') ? 'light' : 'dark';
                apply(next);
            });
        })();
    </script>

    <!-- FOOTER -->
    <x-main-footer />
</body>
</html>
