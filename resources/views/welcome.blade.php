<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>

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

        .dark footer {
            background-color: #111827 !important; /* gray-900 */
        }

        /* Fade suave para todo al cambiar de tema */
        html.theme-fade * {
            transition:
                background-color .35s ease,
                color .35s ease,
                border-color .35s ease,
                fill .35s ease;
        }

        /* Botón de tema: animación */
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
    </style>
</head>

<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-gray-800 dark:text-gray-100 transition-colors duration-300">

    {{-- Prompt de preferencias --}}
    @auth
        @if (is_null($userPreferences))
            <div class="fixed inset-0 bg-gray-800/75 dark:bg-black/70 flex items-center justify-center z-50 p-4"
                 id="preferences-prompt">
                <div class="bg-white dark:bg-gray-900 dark:text-gray-100 rounded-lg shadow-xl p-6 max-w-md text-center">
                    <h3 class="text-xl font-semibold mb-3">¡Personaliza tu búsqueda!</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Aún no has guardado tus preferencias. Añádelas para que podamos mostrarte las propiedades que más te interesan.
                    </p>
                    <div class="mb-4 text-left">
                        <input type="checkbox" id="dont-show-again" class="mr-2">
                        <label for="dont-show-again" class="text-sm text-gray-600 dark:text-gray-300">
                            No volver a mostrar este mensaje
                        </label>
                    </div>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('preferences.edit') }}"
                           class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                            Añadir Preferencias
                        </a>
                        <button type="button" onclick="dismissPrompt()"
                                class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-400 transition dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                            Ahora No
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    {{-- Modal de vista rápida de propiedad --}}
    <div id="property-modal"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-900 dark:text-gray-100 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 relative">
            <button id="property-modal-close"
                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/70 text-white flex items-center justify-center hover:bg-black">
                &times;
            </button>

            <div class="md:flex">
                <div class="md:w-1/2">
                    <img id="property-modal-image"
                         src=""
                         alt="Vista rápida propiedad"
                         class="w-full h-64 md:h-full object-cover rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none">
                </div>

                <div class="md:w-1/2 p-6 space-y-3">
                    <p id="property-modal-type"
                       class="text-xs font-semibold uppercase tracking-wide text-blue-500"></p>

                    <h3 id="property-modal-title"
                        class="text-2xl font-semibold text-gray-900 dark:text-gray-100"></h3>

                    <p id="property-modal-location"
                       class="text-sm text-gray-500 dark:text-gray-300 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot"></i>
                        <span></span>
                    </p>

                    <p id="property-modal-price"
                       class="text-xl font-bold text-blue-600 mt-2"></p>

                    <div id="property-modal-meta"
                         class="flex flex-wrap gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <span id="property-modal-bedrooms"
                              class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-bed"></i>
                            <span></span>
                        </span>
                        <span id="property-modal-bathrooms"
                              class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-bath"></i>
                            <span></span>
                        </span>
                    </div>

                    <p id="property-modal-description"
                       class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed"></p>

                    <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-between items-stretch sm:items-center">
                        <button type="button"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800"
                                data-close-modal>
                            Seguir explorando
                        </button>
                        <a id="property-modal-link"
                           href="#"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Ver detalles completos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <x-main-header />

    {{-- Botón Tema (solo aquí) --}}
    <button id="theme-toggle"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                   bg-white text-gray-800 hover:bg-gray-100
                   dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
            aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    {{-- Filtro principal --}}
    <section id="filter-panel"
             class="bg-white dark:bg-gray-900 dark:text-gray-100 shadow-md w-full py-6
                    fixed md:static top-16 left-0 z-[999]">

        <div class="max-w-6xl mx-auto px-6">
            <form id="property-filter-form" method="GET" action="{{ route('home') }}"
                  class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-6 text-center">

                {{-- Tipo de propiedad --}}
                <div class="w-full md:w-auto">
                    <select name="type" id="type_filter_select"
                            class="w-full md:w-40 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
                        <option value="rent" {{ request('type') === 'rent' ? 'selected' : '' }}>Renta</option>
                        <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Venta</option>
                    </select>
                </div>

                {{-- Ciudad --}}
                <div class="w-full md:w-40">
                    <select name="city" id="city" onchange="this.form.submit()"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
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
                            class="w-full text-left border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400">
                        <span>Precio</span>
                    </button>

                    <div id="price-dropdown"
                         class="hidden absolute top-full mt-2 w-full bg-white dark:bg-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 p-4">
                        <p class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Rango de Precio</p>
                        <div id="price-slider" class="mb-4 mx-3"></div>
                        <div class="flex justify-between items-center text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex items-center gap-1 border rounded-md p-2 dark:border-gray-700">
                                $ <span id="slider-min-value"></span>
                            </div>
                            <div class="text-gray-400">-</div>
                            <div class="flex items-center gap-1 border rounded-md p-2 dark:border-gray-700">
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

        {{-- Propiedades basadas en tus preferencias (sin vendidas) --}}
        @auth
            @php
                $recommendedVisible = collect($recommendedProperties ?? [])->where('status', '!=', 'sold');
            @endphp

            @if ($userPreferences && $recommendedVisible->isNotEmpty())
                <section>
                    <h2 class="text-2xl font-semibold mb-1">
                        Propiedades basadas en tus preferencias
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Te mostramos propiedades que coinciden con la ciudad, rango de precio y características que guardaste en tus preferencias.
                    </p>

                    <div class="relative group">
                        <button
                            class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                            <i class="fas fa-chevron-left text-gray-700 dark:text-gray-200 text-lg"></i>
                        </button>

                        <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                            @foreach ($recommendedVisible as $property)
                                @php
                                    $favoriteIds = $favoriteIds ?? [];
                                    $isFav = in_array($property->id, $favoriteIds);
                                    $firstImage = $property->images->first();
                                    $imageUrl = $firstImage
                                        ? asset('storage/' . $firstImage->image_path)
                                        : 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                                @endphp

                                <a href="{{ route('properties.show', $property) }}"
                                   class="block w-64 bg-white dark:bg-gray-900 rounded-xl border border-transparent dark:border-gray-800 shadow hover:shadow-lg transition flex-shrink-0 js-open-property-modal"
                                   data-title="{{ $property->title }}"
                                   data-location="{{ $property->location ?? 'Ubicación no especificada' }}"
                                   data-price="${{ number_format($property->price, 2) }}"
                                   data-type="{{ $property->listing_type === 'rent' ? 'Renta' : 'Venta' }}"
                                   data-url="{{ route('properties.show', $property) }}"
                                   data-image="{{ $imageUrl }}"
                                   data-bedrooms="{{ $property->bedrooms ?? '' }}"
                                   data-bathrooms="{{ $property->bathrooms ?? '' }}"
                                   data-description="{{ \Illuminate\Support\Str::limit($property->description, 150) }}"
                                >
                                    <div class="relative">
                                        <img src="{{ $imageUrl }}"
                                             alt="Imagen de {{ $property->title }}"
                                             class="w-full h-48 object-cover rounded-t-xl">

                                        {{-- Botón favoritos --}}
                                        <div class="absolute top-2 right-2 bg-white/80 dark:bg-gray-800/80 rounded-full p-2 shadow-sm">
                                            @auth
                                                <form method="POST"
                                                      action="{{ $isFav ? route('favorites.destroy', $property) : route('favorites.store', $property) }}"
                                                      id="fav-form-{{ $property->id }}-rec"
                                                      class="hidden">
                                                    @csrf
                                                    @if($isFav) @method('DELETE') @endif
                                                </form>
                                                <button type="button"
                                                        class="favorite-btn"
                                                        data-favorite-toggle="1"
                                                        data-toggle-url="{{ route('favorites.toggle', $property) }}"
                                                        data-state="{{ $isFav ? 'on' : 'off' }}"
                                                        data-fallback-form-id="fav-form-{{ $property->id }}-rec">
                                                    <i class="{{ $isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-600 dark:text-gray-200' }} fa-heart text-lg"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                        class="favorite-btn"
                                                        data-login-url="{{ route('login') }}">
                                                    <i class="fa-regular fa-heart text-gray-600 dark:text-gray-200 text-lg"></i>
                                                </button>
                                            @endauth
                                        </div>
                                    </div>

                                    <div class="p-3">
                                        <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-300 truncate mt-1">
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
                            class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                            <i class="fas fa-chevron-right text-gray-700 dark:text-gray-200 text-lg"></i>
                        </button>
                    </div>
                </section>
            @endif
        @endauth

        @if($noResults)
            <div class="text-center py-20 text-gray-600 dark:text-gray-300 text-xl font-semibold">
                No encontramos propiedades con los filtros seleccionados.
                <br>
                <span class="text-sm block mt-2 text-gray-500">Prueba ajustando el rango de precio o la ciudad.</span>
            </div>
        @else
            {{-- aquí siguen los carouseles por ciudad --}}
        @endif

        {{-- Propiedades por ciudad (sin vendidas) --}}
        @foreach($propertiesByCity as $city => $cityProperties)
            @php
                $cityPropertiesVisible = collect($cityProperties)->where('status', '!=', 'sold');
            @endphp

            @if ($cityPropertiesVisible->isEmpty())
                @continue
            @endif

            <section>
                <h2 class="text-2xl font-semibold mb-4">Propiedades en {{ $city }}</h2>
                <div class="relative group">
                    <button
                        class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-chevron-left text-gray-700 dark:text-gray-200 text-lg"></i>
                    </button>

                    <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                        @foreach($cityPropertiesVisible as $property)
                            @php
                                $favoriteIds = $favoriteIds ?? [];
                                $isFav = in_array($property->id, $favoriteIds);
                                $firstImage = $property->images->first();
                                $imageUrl = $firstImage
                                    ? asset('storage/' . $firstImage->image_path)
                                    : 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                            @endphp

                            <a href="{{ route('properties.show', $property) }}"
                               class="block w-64 bg-white dark:bg-gray-900 rounded-xl border border-transparent dark:border-gray-800 shadow hover:shadow-lg transition flex-shrink-0 js-open-property-modal"
                               data-title="{{ $property->title }}"
                               data-location="{{ $property->location ?? 'Ubicación no especificada' }}"
                               data-price="${{ number_format($property->price, 2) }}"
                               data-type="{{ $property->listing_type === 'rent' ? 'Renta' : 'Venta' }}"
                               data-url="{{ route('properties.show', $property) }}"
                               data-image="{{ $imageUrl }}"
                               data-bedrooms="{{ $property->bedrooms ?? '' }}"
                               data-bathrooms="{{ $property->bathrooms ?? '' }}"
                               data-description="{{ \Illuminate\Support\Str::limit($property->description, 150) }}"
                            >
                                <div class="relative">
                                    <img src="{{ $imageUrl }}"
                                         alt="Imagen de {{ $property->title }}"
                                         class="w-full h-48 object-cover rounded-t-xl">

                                    <div class="absolute top-2 right-2 bg-white/80 dark:bg-gray-800/80 rounded-full p-2 shadow-sm">
                                        @auth
                                            <form method="POST"
                                                  action="{{ $isFav ? route('favorites.destroy', $property) : route('favorites.store', $property) }}"
                                                  id="fav-form-{{ $property->id }}-city-{{ $city }}"
                                                  class="hidden">
                                                @csrf
                                                @if($isFav) @method('DELETE') @endif
                                            </form>
                                            <button type="button"
                                                    class="favorite-btn"
                                                    data-favorite-toggle="1"
                                                    data-toggle-url="{{ route('favorites.toggle', $property) }}"
                                                    data-state="{{ $isFav ? 'on' : 'off' }}"
                                                    data-fallback-form-id="fav-form-{{ $property->id }}-city-{{ $city }}">
                                                <i class="{{ $isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-600 dark:text-gray-200' }} fa-heart text-lg"></i>
                                            </button>
                                        @else
                                            <button type="button"
                                                    class="favorite-btn"
                                                    data-login-url="{{ route('login') }}">
                                                <i class="fa-regular fa-heart text-gray-600 dark:text-gray-200 text-lg"></i>
                                            </button>
                                        @endauth
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 truncate mt-1">
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
                        class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                        <i class="fas fa-chevron-right text-gray-700 dark:text-gray-200 text-lg"></i>
                    </button>
                </div>
            </section>
        @endforeach

        {{-- Propiedades recientes (sin vendidas) --}}
        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades Recientes</h2>
            <div class="relative group">
                <button
                    class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                    <i class="fas fa-chevron-left text-gray-700 dark:text-gray-200 text-lg"></i>
                </button>

                @php
                    $recentVisible = collect($properties)->where('status', '!=', 'sold');
                @endphp

                <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                    @forelse ($recentVisible as $property)
                        @php
                            $favoriteIds = $favoriteIds ?? [];
                            $isFav = in_array($property->id, $favoriteIds);
                            $firstImage = $property->images->first();
                            $imageUrl = $firstImage
                                ? asset('storage/' . $firstImage->image_path)
                                : 'https://via.placeholder.com/300x200?text=Sin+Imagen';
                        @endphp

                        <a href="{{ route('properties.show', $property) }}"
                           class="block w-64 bg-white dark:bg-gray-900 rounded-xl border border-transparent dark:border-gray-800 shadow hover:shadow-lg transition flex-shrink-0 js-open-property-modal"
                           data-title="{{ $property->title }}"
                           data-location="{{ $property->location ?? 'Ubicación no especificada' }}"
                           data-price="${{ number_format($property->price, 2) }}"
                           data-type="{{ $property->listing_type === 'rent' ? 'Renta' : 'Venta' }}"
                           data-url="{{ route('properties.show', $property) }}"
                           data-image="{{ $imageUrl }}"
                           data-bedrooms="{{ $property->bedrooms ?? '' }}"
                           data-bathrooms="{{ $property->bathrooms ?? '' }}"
                           data-description="{{ \Illuminate\Support\Str::limit($property->description, 150) }}"
                        >
                            <div class="relative">
                                <img src="{{ $imageUrl }}"
                                     alt="Imagen de {{ $property->title }}"
                                     class="w-full h-48 object-cover rounded-t-xl">

                                <div class="absolute top-2 right-2 bg-white/80 dark:bg-gray-800/80 rounded-full p-2 shadow-sm">
                                    @auth
                                        <form method="POST"
                                              action="{{ $isFav ? route('favorites.destroy', $property) : route('favorites.store', $property) }}"
                                              id="fav-form-{{ $property->id }}-recent"
                                              class="hidden">
                                            @csrf
                                            @if($isFav) @method('DELETE') @endif
                                        </form>
                                        <button type="button"
                                                class="favorite-btn"
                                                data-favorite-toggle="1"
                                                data-toggle-url="{{ route('favorites.toggle', $property) }}"
                                                data-state="{{ $isFav ? 'on' : 'off' }}"
                                                data-fallback-form-id="fav-form-{{ $property->id }}-recent">
                                            <i class="{{ $isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-600 dark:text-gray-200' }} fa-heart text-lg"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                class="favorite-btn"
                                                data-login-url="{{ route('login') }}">
                                            <i class="fa-regular fa-heart text-gray-600 dark:text-gray-200 text-lg"></i>
                                        </button>
                                    @endauth
                                </div>
                            </div>

                            <div class="p-3">
                                <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-300 truncate mt-1">
                                    {{ $property->location ?? 'Ubicación no especificada' }}
                                </p>
                                <p class="mt-2 font-semibold text-blue-600">
                                    ${{ number_format($property->price, 2) }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 dark:text-gray-300">Aún no hay propiedades para mostrar.</p>
                    @endforelse
                </div>

                <button
                    class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white dark:bg-gray-800 shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200 dark:border-gray-700">
                    <i class="fas fa-chevron-right text-gray-700 dark:text-gray-200 text-lg"></i>
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
        // ===== Tema (claro/oscuro) + toggle + icono + animación + fade =====
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
            const saleMin  = {{ $saleMinRange ?? 1000000 }};
            const saleMax  = {{ $saleMaxRange ?? 20000000 }};

            let minRange, maxRange, step;
            if (currentType === 'rent') {
                minRange = rentMin; maxRange = rentMax; step = 100;
            } else {
                minRange = saleMin; maxRange = saleMax; step = 100000;
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
    </script>

    <script>
        // ==== Modal de vista rápida de propiedad ====
        (function () {
            const modal = document.getElementById('property-modal');
            if (!modal) return;

            const imgEl = document.getElementById('property-modal-image');
            const titleEl = document.getElementById('property-modal-title');
            const typeEl = document.getElementById('property-modal-type');
            const locationContainer = document.getElementById('property-modal-location');
            const locationText = locationContainer ? locationContainer.querySelector('span') : null;
            const priceEl = document.getElementById('property-modal-price');
            const descEl = document.getElementById('property-modal-description');
            const linkEl = document.getElementById('property-modal-link');

            const bedroomsWrap = document.getElementById('property-modal-bedrooms');
            const bedroomsText = bedroomsWrap ? bedroomsWrap.querySelector('span') : null;
            const bathroomsWrap = document.getElementById('property-modal-bathrooms');
            const bathroomsText = bathroomsWrap ? bathroomsWrap.querySelector('span') : null;

            const closeBtn = document.getElementById('property-modal-close');

            function openModalFromCard(card) {
                if (!card) return;

                const title = card.dataset.title || '';
                const location = card.dataset.location || '';
                const price = card.dataset.price || '';
                const type = card.dataset.type || '';
                const url = card.dataset.url || '#';
                const img = card.dataset.image || '';
                const bedrooms = card.dataset.bedrooms || '';
                const bathrooms = card.dataset.bathrooms || '';
                const desc = card.dataset.description || '';

                if (imgEl && img) imgEl.src = img;
                if (titleEl) titleEl.textContent = title;
                if (typeEl) typeEl.textContent = type;
                if (locationText) locationText.textContent = location;
                if (priceEl) priceEl.textContent = price;
                if (descEl) descEl.textContent = desc;
                if (linkEl) linkEl.href = url;

                if (bedroomsWrap) {
                    if (bedrooms) {
                        bedroomsWrap.classList.remove('hidden');
                        if (bedroomsText) bedroomsText.textContent = bedrooms + ' hab.';
                    } else {
                        bedroomsWrap.classList.add('hidden');
                    }
                }

                if (bathroomsWrap) {
                    if (bathrooms) {
                        bathroomsWrap.classList.remove('hidden');
                        if (bathroomsText) bathroomsText.textContent = bathrooms + ' baños';
                    } else {
                        bathroomsWrap.classList.add('hidden');
                    }
                }

                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            // Abrir modal al hacer click en una tarjeta de propiedad
            document.querySelectorAll('.js-open-property-modal').forEach(card => {
                card.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    openModalFromCard(this);
                });
            });

            // Cerrar modal
            closeBtn?.addEventListener('click', closeModal);

            modal.addEventListener('click', function (ev) {
                if (ev.target === modal) {
                    closeModal();
                }
            });

            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function (ev) {
                if (ev.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();

        // ==== Botones de favoritos en las tarjetas de la home ====
        (function () {
            const csrf = '{{ csrf_token() }}';

            document.querySelectorAll('.favorite-btn').forEach(btn => {
                btn.addEventListener('click', async function (ev) {
                    ev.stopPropagation();
                    ev.preventDefault();

                    const loginUrl = this.dataset.loginUrl;
                    const toggleUrl = this.dataset.toggleUrl;
                    const fallbackFormId = this.dataset.fallbackFormId;

                    // Si hay loginUrl, el usuario no está logueado
                    if (loginUrl && !toggleUrl) {
                        window.location.href = loginUrl;
                        return;
                    }

                    // Si no hay fetch, usa el formulario como fallback
                    if (!window.fetch) {
                        if (fallbackFormId) {
                            const form = document.getElementById(fallbackFormId);
                            if (form) form.submit();
                        }
                        return;
                    }

                    try {
                        const res = await fetch(toggleUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) {
                            // Si por alguna razón nos manda a login
                            if (loginUrl) {
                                window.location.href = loginUrl;
                            }
                            return;
                        }

                        const data = await res.json();
                        if (!data || !data.ok) {
                            if (loginUrl) {
                                window.location.href = loginUrl;
                            }
                            return;
                        }

                        const on = data.favorited === true;
                        this.dataset.state = on ? 'on' : 'off';

                        const icon = this.querySelector('i');
                        if (icon) {
                            if (on) {
                                icon.classList.remove('fa-regular', 'text-gray-600', 'dark:text-gray-200');
                                icon.classList.add('fa-solid', 'text-red-500');
                            } else {
                                icon.classList.remove('fa-solid', 'text-red-500');
                                icon.classList.add('fa-regular', 'text-gray-600', 'dark:text-gray-200');
                            }
                        }
                    } catch (e) {
                        // En caso de error, usa el fallback form si existe
                        if (fallbackFormId) {
                            const form = document.getElementById(fallbackFormId);
                            if (form) form.submit();
                        } else if (loginUrl) {
                            window.location.href = loginUrl;
                        }
                    }
                });
            });
        })();
    </script>
</body>
</html>
