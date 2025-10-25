<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Añadido Font Awesome para los iconos del nuevo diseño --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Pequeño helper para ocultar la barra de scroll en el carrusel */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- ========================================================== --}}
    {{-- ==        PROMPT DE PREFERENCIAS (DEL welcomeold)       == --}}
    {{-- ========================================================== --}}
    @auth
        @if (is_null($userPreferences))
            <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 p-4" id="preferences-prompt">
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
                        <a href="{{ route('preferences.edit') }}" class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
                            Añadir Preferencias
                        </a>
                        <button type="button" onclick="dismissPrompt()" class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-400 transition">
                            Ahora No
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth
    {{-- ========================================================== --}}


    {{-- HEADER (del nuevo welcome.blade.php, con botón de modal login del welcomeold) --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                <i class="fas fa-home mr-2"></i> {{-- Icono del nuevo diseño --}}
                Sin beca<span class="text-gray-700"> no hay renta </span>
            </div>
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                 <a href="{{ route('visits.my') }}" class="hover:text-blue-600 transition">Mis Visitas</a>
                <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
                @auth
                    @if(auth()->user()->role === 'agent')
                    <a href="{{ route('agent.home') }}" class="hover:text-blue-600 transition">Panel de Agente</a>
                    @else
                    <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Modo vendedor</a>
                    @endif
                    <a href="{{ url('/dashboard') }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
                @else
                    <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Modo vendedor</a>
                    {{-- Botón para abrir modal (del welcomeold) --}}
                    <button type="button" onclick="openLoginModal()"
                            class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
                        Iniciar sesión
                    </button>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Buscador (del nuevo welcome.blade.php) --}}
    <section class="bg-white shadow-sm w-full py-4">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-3 px-6">
            <input type="text" placeholder="¿Dónde buscas?"
                class="w-full md:w-1/3 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="date"
                class="w-full md:w-1/4 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="date"
                class="w-full md:w-1/4 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="w-full md:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                Buscar
            </button>
        </div>
    </section>

    {{-- Contenido Principal --}}
    <main class="max-w-7xl mx-auto mt-10 px-6 space-y-12">

        {{-- Carrusel Propiedades Recientes (del nuevo welcome.blade.php) --}}
        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades Recientes</h2>
            <div class="relative group">
                <button class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                </button>
                <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                    @forelse ($properties as $property)
                    <a href="{{ route('properties.show', $property) }}"
                        class="block w-64 bg-white rounded-xl shadow hover:shadow-lg transition flex-shrink-0">
                        @if ($property->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                            alt="Imagen de {{ $property->title }}" class="w-full h-48 object-cover rounded-t-xl">
                        @else
                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                            class="w-full h-48 object-cover rounded-t-xl">
                        @endif
                        <div class="p-3">
                            <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                            <p class="text-sm text-gray-500 truncate mt-1">{{ $property->location ?? 'Ubicación no especificada' }}</p>
                            <p class="mt-2 font-semibold text-blue-600">${{ number_format($property->price, 2) }}</p>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-500">Aún no hay propiedades para mostrar.</p>
                    @endforelse
                </div>
                <button class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                </button>
            </div>
        </section>

        {{-- Sección Recomendaciones (del welcomeold.blade.php) --}}
        @auth
            @if ($recommendedProperties->isNotEmpty())
                <section>
                    <h2 class="text-2xl font-semibold mb-4">Recomendado para Ti</h2>
                    {{-- Puedes usar el mismo estilo de carrusel aquí si quieres --}}
                    <div class="relative group">
                         <button class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                            <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                        </button>
                        <div class="carousel-container flex space-x-4 overflow-x-auto scrollbar-hide pb-4 scroll-smooth mx-2">
                            @foreach ($recommendedProperties as $property)
                                <a href="{{ route('properties.show', $property) }}" class="block w-64 bg-white rounded-xl shadow hover:shadow-lg transition flex-shrink-0">
                                     @if ($property->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                                             alt="Imagen de {{ $property->title }}" class="w-full h-48 object-cover rounded-t-xl">
                                    @else
                                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                                             class="w-full h-48 object-cover rounded-t-xl">
                                    @endif
                                    <div class="p-3">
                                        <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500 truncate mt-1">{{ $property->location ?? 'Ubicación no especificada' }}</p>
                                        <p class="mt-2 font-semibold text-blue-600">${{ number_format($property->price, 2) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <button class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                            <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                        </button>
                    </div>
                </section>
            @endif
        @endauth

    </main>

    {{-- Footer (del nuevo welcome.blade.php) --}}
    <footer class="bg-gray-800 text-white py-8 mt-auto">
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
                    <ul class="space-y-2">
                        <li><a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mis Visitas</a></li>
                        <li><a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mapa</a></li>
                        <li><a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Panel de Agente</a></li>
                        <li><a href="{{ route('agent.view') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Modo Vendedor</a></li>
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
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Privacidad</a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Términos</a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- ===================== LOGIN MODAL (del welcomeold) ===================== --}}
    <div id="loginModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md relative">
            <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="mb-6 text-center">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Iniciar Sesión</h3>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="email" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    @error('email') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input id="password" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="password" name="password" required autocomplete="current-password" />
                    @error('password') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="block mb-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Recuérdame</span>
                    </label>
                </div>
                <div class="flex items-center justify-end mb-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">
                    Iniciar Sesión
                </button>
                <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                    ¿No tienes cuenta? <a href="{{ route('register') }}" class="underline hover:text-blue-600 dark:hover:text-blue-400">Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>
    {{-- =================== END LOGIN MODAL =================== --}}

    {{-- SCRIPTS --}}

    {{-- Script para el prompt de preferencias (del welcomeold) --}}
    <script>
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
    </script>

    {{-- Script para el modal de login (del welcomeold) --}}
    <script>
        const loginModal = document.getElementById('loginModal');
        function openLoginModal() {
            if (loginModal) {
                loginModal.classList.remove('hidden');
            }
        }
        function closeLoginModal() {
            if (loginModal) {
                loginModal.classList.add('hidden');
            }
        }
        window.addEventListener('click', function(event) {
            if (event.target === loginModal) {
                closeLoginModal();
            }
        });
    </script>

    {{-- Script para los carruseles (del nuevo welcome.blade.php) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.relative.group'); // Selecciona los contenedores de carrusel
            carousels.forEach(carousel => {
                const container = carousel.querySelector('.carousel-container');
                const prevBtn = carousel.querySelector('.carousel-prev');
                const nextBtn = carousel.querySelector('.carousel-next');
                const scrollAmount = 300; // Ajusta según necesites

                if(container && prevBtn && nextBtn) { // Asegura que los elementos existan
                    prevBtn.addEventListener('click', () => {
                        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    });
                    nextBtn.addEventListener('click', () => {
                        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    });
                }
            });
        });
    </script>

</body>
</html>
