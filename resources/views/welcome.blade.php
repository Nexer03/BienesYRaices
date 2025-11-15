<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Iconos --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- noUiSlider --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">

    <style>
        /* Ocultar scroll en carruseles */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- Prompt de preferencias --}}
    @auth
        @if (is_null($userPreferences))
            <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 p-4"
                 id="preferences-prompt">
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-md text-center">
                    <h3 class="text-xl font-semibold mb-3">¡Personaliza tu búsqueda!</h3>
                    <p class="text-gray-600 mb-4">
                        Aún no has guardado tus preferencias. Añádelas para que podamos mostrarte las propiedades que más te interesan.
                    </p>
                    <div class="mb-4 text-left">
                        <input type="checkbox" id="dont-show-again" class="mr-2">
                        <label for="dont-show-again" class="text-sm text-gray-600">No volver a mostrar este mensaje</label>
                    </div>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('preferences.edit') }}"
                           class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                            Añadir Preferencias
                        </a>
                        <button type="button" onclick="dismissPrompt()"
                                class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-400 transition">
                            Ahora No
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- HEADER --}}
    <x-main-header />

    {{-- Filtro principal --}}
    <section id="filter-panel"
             class="bg-white shadow-md w-full py-6 md:translate-y-0 transform -translate-y-full transition-transform duration-300 fixed md:static top-16 md:top-auto left-0 z-40 md:z-0">
        <div class="max-w-6xl mx-auto px-6">
            <form id="property-filter-form" method="GET" action="{{ route('home') }}"
                  class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-6 text-center">

                {{-- Tipo de propiedad --}}
                <div class="w-full md:w-auto">
                    <select name="type" id="type_filter_select"
                            class="w-full md:w-40 border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="rent" {{ request('type') === 'rent' ? 'selected' : '' }}>Renta</option>
                        <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Venta</option>
                    </select>
                </div>

                {{-- Ciudad --}}
                <div class="w-full md:w-40">
                    <select name="city" id="city" onchange="this.form.submit()"
                            class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="">Todas las ciudades</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro de precio --}}
                <div class="relative w-full md:w-72">
                    <button type="button" id="price-filter-button"
                            class="w-full text-left border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
                        <span>Precio</span>
                    </button>

                    <div id="price-dropdown"
                         class="hidden absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-xl z-50 p-4">
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
                        <input type="hidden" name="min_price" id="slider-min-input">
                        <input type="hidden" name="max_price" id="slider-max-input">
                        <div class="mt-4 text-right">
                            <button type="button" id="apply-price-button"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                Aplicar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Botón buscar --}}
                <div class="w-full md:w-auto">
                    <button type="submit"
                            class="w-full md:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Contenido Principal --}}
    <main class="max-w-7xl mx-auto mt-10 px-6 space-y-12">

        {{-- Recomendaciones --}}
        @auth
            @if ($recommendedProperties->isNotEmpty())
                <section>
                    <h2 class="text-2xl font-semibold mb-4">Recomendado para Ti según tus preferencias.</h2>
                    <div class="relative group">
                        <button
                            class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                            <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                        </button>
                        <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                            @foreach ($recommendedProperties as $property)
                                <a href="{{ route('properties.show', $property) }}"
                                   class="block w-64 bg-white rounded-xl shadow hover:shadow-lg transition flex-shrink-0">
                                    <div class="relative">
                                        @if ($property->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                                                 alt="Imagen de {{ $property->title }}"
                                                 class="w-full h-48 object-cover rounded-t-xl">
                                        @else
                                            <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                                                 class="w-full h-48 object-cover rounded-t-xl">
                                        @endif

                                        <div class="absolute top-2 right-2 bg-white/80 rounded-full p-2 shadow-sm">
                                            <i class="fa-regular fa-heart text-gray-600 text-lg"></i>
                                        </div>
                                    </div>

                                    <div class="p-3">
                                        <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500 truncate mt-1">
                                            {{ $property->location ?? 'Ubicación no especificada' }}
                                        </p>
                                        <p class="mt-2 font-semibold text-blue-600">
                                            ${{ number_format($property->price, 2) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <button
                            class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                            <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                        </button>
                    </div>
                </section>
            @endif
        @endauth

        {{-- Propiedades por ciudad --}}
        @foreach($propertiesByCity as $city => $cityProperties)
            <section>
                <h2 class="text-2xl font-semibold mb-4">Propiedades en {{ $city }}</h2>
                <div class="relative group">
                    <button
                        class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                        <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                    </button>
                    <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                        @foreach($cityProperties as $property)
                            <a href="{{ route('properties.show', $property) }}"
                               class="block w-64 bg-white rounded-xl shadow hover:shadow-lg transition flex-shrink-0">
                                <div class="relative">
                                    @if($property->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                                             alt="Imagen de {{ $property->title }}"
                                             class="w-full h-48 object-cover rounded-t-xl">
                                    @else
                                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen"
                                             alt="Sin imagen disponible"
                                             class="w-full h-48 object-cover rounded-t-xl">
                                    @endif

                                    <div class="absolute top-2 right-2 bg-white/80 rounded-full p-2 shadow-sm">
                                        <i class="fa-regular fa-heart text-gray-600 text-lg"></i>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                    <p class="text-sm text-gray-500 truncate mt-1">
                                        {{ $property->location ?? 'Ubicación no especificada' }}
                                    </p>
                                    <p class="mt-2 font-semibold text-blue-600">
                                        ${{ number_format($property->price, 2) }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <button
                        class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                        <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                    </button>
                </div>
            </section>
        @endforeach

        {{-- Propiedades recientes --}}
        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades Recientes</h2>
            <div class="relative group">
                <button
                    class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                </button>
                <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                    @forelse ($properties as $property)
                        <a href="{{ route('properties.show', $property) }}"
                           class="block w-64 bg-white rounded-xl shadow hover:shadow-lg transition flex-shrink-0">
                            <div class="relative">
                                @if ($property->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                                         alt="Imagen de {{ $property->title }}"
                                         class="w-full h-48 object-cover rounded-t-xl">
                                @else
                                    <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                                         class="w-full h-48 object-cover rounded-t-xl">
                                @endif

                                <div class="absolute top-2 right-2 bg-white/80 rounded-full p-2 shadow-sm">
                                    <i class="fa-regular fa-heart text-gray-600 text-lg"></i>
                                </div>
                            </div>

                            <div class="p-3">
                                <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                <p class="text-sm text-gray-500 truncate mt-1">
                                    {{ $property->location ?? 'Ubicación no especificada' }}
                                </p>
                                <p class="mt-2 font-semibold text-blue-600">
                                    ${{ number_format($property->price, 2) }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500">Aún no hay propiedades para mostrar.</p>
                    @endforelse
                </div>
                <button
                    class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                </button>
            </div>
        </section>

    </main>

    {{-- Footer --}}
    <x-main-footer />

    {{-- JS de noUiSlider (debe ir antes del script que lo usa) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

    {{-- Lógica principal de la página --}}
    <script>
        // Prompt de preferencias
        const promptElement = document.getElementById('preferences-prompt');
        const dontShowCheckbox = document.getElementById('dont-show-again');

        function dismissPrompt() {
            if (dontShowCheckbox && dontShowCheckbox.checked) {
                localStorage.setItem('hidePreferencePrompt', 'true');
            }
            if (promptElement) {
                promptElement.style.display = 'none';
            }
        }

        if (promptElement && localStorage.getItem('hidePreferencePrompt') === 'true') {
            promptElement.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Carruseles
            document.querySelectorAll('.relative.group').forEach(carousel => {
                const container = carousel.querySelector('.carousel-container');
                const prevBtn = carousel.querySelector('.carousel-prev');
                const nextBtn = carousel.querySelector('.carousel-next');
                const scrollAmount = 300;

                if (container && prevBtn && nextBtn) {
                    prevBtn.addEventListener('click', () => {
                        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    });
                    nextBtn.addEventListener('click', () => {
                        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    });
                }
            });

            // Slider de precio y filtros
            const priceSlider      = document.getElementById('price-slider');
            const minInput         = document.getElementById('slider-min-input');
            const maxInput         = document.getElementById('slider-max-input');
            const typeFilterSelect = document.getElementById('type_filter_select');
            const filterForm       = document.getElementById('property-filter-form');

            if (typeFilterSelect && filterForm) {
                typeFilterSelect.addEventListener('change', function () {
                    if (minInput) minInput.value = '';
                    if (maxInput) maxInput.value = '';
                    filterForm.submit();
                });
            }

            if (!priceSlider) {
                return;
            }

            const minDisplay       = document.getElementById('slider-min-value');
            const maxDisplay       = document.getElementById('slider-max-value');
            const priceButton      = document.getElementById('price-filter-button');
            const priceDropdown    = document.getElementById('price-dropdown');
            const applyPriceButton = document.getElementById('apply-price-button');

            const currentType = '{{ $typeFilter }}';
            const rentMin  = {{ $rentMinRange ?? 0 }};
            const rentMax  = {{ $rentMaxRange ?? 10000 }};
            const saleMin  = {{ $saleMinRange ?? 500000 }};
            const saleMax  = {{ $saleMaxRange ?? 10000000 }};

            let minRange, maxRange, step;
            if (currentType === 'rent') {
                minRange = rentMin; maxRange = rentMax; step = 100;
            } else {
                minRange = saleMin; maxRange = saleMax; step = 50000;
            }

            const currentMinFromRequest = {{ request('min_price', 'null') }};
            const currentMaxFromRequest = {{ request('max_price', 'null') }};

            const currentMin = currentMinFromRequest !== null ? Number(currentMinFromRequest) : minRange;
            const currentMax = currentMaxFromRequest !== null ? Number(currentMaxFromRequest) : maxRange;

            const formatter = new Intl.NumberFormat('es-MX', { style: 'decimal', maximumFractionDigits: 0 });

            function updateButtonText(minVal, maxVal) {
                if (minVal > minRange || maxVal < maxRange) {
                    priceButton.innerHTML = `<span>$${formatter.format(minVal)} - $${formatter.format(maxVal)}</span>`;
                } else {
                    priceButton.innerHTML = `<span>Precio</span>`;
                }
            }

            noUiSlider.create(priceSlider, {
                start: [currentMin, currentMax],
                connect: true,
                step: step,
                range: { 'min': minRange, 'max': maxRange }
            });

            priceSlider.noUiSlider.on('update', function (values) {
                const minValue = parseFloat(values[0]);
                const maxValue = parseFloat(values[1]);

                minDisplay.textContent = formatter.format(minValue);
                maxDisplay.textContent = formatter.format(maxValue);

                minInput.value = minValue;
                maxInput.value = maxValue;

                updateButtonText(minValue, maxValue);
            });

            priceButton.addEventListener('click', (e) => {
                e.stopPropagation();
                priceDropdown.classList.toggle('hidden');
            });

            applyPriceButton.addEventListener('click', () => priceDropdown.classList.add('hidden'));

            window.addEventListener('click', (e) => {
                if (!priceDropdown.classList.contains('hidden') &&
                    !priceDropdown.contains(e.target) &&
                    e.target !== priceButton) {
                    priceDropdown.classList.add('hidden');
                }
            });

            updateButtonText(currentMin, currentMax);
        });

        // Filtro en móvil (search toggle)
        (function () {
            const searchToggle = document.getElementById('search-toggle');
            const filterPanel  = document.getElementById('filter-panel');
            if (!searchToggle || !filterPanel) return;

            let isFilterVisible = false;

            function toggleFilter() {
                if (window.innerWidth < 768) {
                    isFilterVisible = !isFilterVisible;
                    filterPanel.style.transform = isFilterVisible ? 'translateY(0)' : 'translateY(-100%)';
                }
            }

            searchToggle.addEventListener('click', toggleFilter);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    filterPanel.style.transform = 'translateY(0)';
                } else if (!isFilterVisible) {
                    filterPanel.style.transform = 'translateY(-100%)';
                }
            });
        })();

        // Tema oscuro
        (function () {
            const themeToggle = document.getElementById('theme-toggle');
            if (!themeToggle) return;
            const html = document.documentElement;
            const icon = themeToggle.querySelector('i');

            if (localStorage.theme === 'dark') {
                html.classList.add('dark');
                icon.classList.replace('fa-moon', 'fa-sun');
            }

            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                const isDark = html.classList.contains('dark');
                icon.classList.toggle('fa-moon', !isDark);
                icon.classList.toggle('fa-sun', isDark);
                localStorage.theme = isDark ? 'dark' : 'light';
            });
        })();
    </script>
</body>
</html>
