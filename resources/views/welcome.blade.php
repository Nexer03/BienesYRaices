<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* Pequeño helper para ocultar la barra de scroll en el carrusel */
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
                        {{-- El onclick llama a la función global --}}
                        <button type="button" onclick="dismissPrompt()" class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-400 transition">
                            Ahora No
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                Sin beca<span class="text-gray-700"> no hay renta </span>
            </div>
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                 <a href="{{ route('visits.my') }}" class="hover:text-blue-600 transition">Mis Visitas</a>

                <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>

                @auth
                @if(auth()->user()->role === 'agent')
                {{-- Si YA es agente, va directo al panel --}}
                <a href="{{ route('agent.home') }}" class="hover:text-blue-600 transition">Panel de Agente</a>
                @else
                {{-- Si NO es agente, va al formulario --}}
                <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Modo vendedor</a>
                @endif

                <a href="{{ url('/dashboard') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
                @else
                <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Modo vendedor</a>
                {{-- REEMPLAZADO CON EL BOTÓN --}}
     <button type="button" onclick="openLoginModal()"
             class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
         Iniciar sesión
     </button>
                @endauth
            </nav>
        </div>
    </header>

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



    <main class="max-w-7xl mx-auto mt-10 px-6 space-y-12">

        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades Recientes</h2>
            <div class="relative">
                <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4">
                    @forelse ($properties as $property)
                    <a href="{{ route('properties.show', $property) }}"
                        class="block min-w-[250px] bg-white rounded-xl shadow hover:shadow-lg transition">
                        {{-- Imagen de la propiedad --}}
                        @if ($property->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                            alt="Imagen de {{ $property->title }}" class="w-full h-48 object-cover rounded-t-xl">
                        @else
                        {{-- Imagen por defecto si no hay fotos --}}
                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                            class="w-full h-48 object-cover rounded-t-xl">
                        @endif

                        <div class="p-3">
                            <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $property->location ?? 'Ubicación no especificada' }}
                            </p>
                            <p class="mt-1 font-semibold">${{ number_format($property->price, 2) }}</p>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-500">Aún no hay propiedades para mostrar.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ========================================================== --}}
        {{-- == NUEVA SECCIÓN: Recomendaciones Basadas en Preferencias == --}}
        {{-- ========================================================== --}}
        @auth {{-- Solo muestra esta sección si el usuario está logueado --}}
            @if ($recommendedProperties->isNotEmpty()) {{-- Y si hay recomendaciones --}}
                <section>
                    <h2 class="text-2xl font-semibold mb-4">Recomendado para Ti</h2>
                    <div class="relative">
                        <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4">
                            {{-- Usamos la misma estructura de tarjeta que antes --}}
                            @foreach ($recommendedProperties as $property)
                                <a href="{{ route('properties.show', $property) }}" class="block min-w-[250px] bg-white rounded-xl shadow hover:shadow-lg transition">
                                    @if ($property->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                                            alt="Imagen de {{ $property->title }}" class="w-full h-48 object-cover rounded-t-xl">
                                    @else
                                        <img src="https://via.placeholder.com/300x200?text=Sin+Imagen" alt="Sin imagen disponible"
                                            class="w-full h-48 object-cover rounded-t-xl">
                                    @endif
                                    <div class="p-3">
                                        <h3 class="font-semibold text-lg truncate">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $property->location ?? 'Ubicación no especificada' }}</p>
                                        <p class="mt-1 font-semibold">${{ number_format($property->price, 2) }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endauth

    </main>

    <footer class="bg-gray-100 mt-20 w-full py-10 border-t">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 px-6 text-sm text-gray-600">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Soporte</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Centro de ayuda</a></li>
                    <li><a href="#" class="hover:text-blue-600">Preguntas frecuentes</a></li>
                    <li><a href="#" class="hover:text-blue-600">Reportar problema</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Compañía</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Sobre nosotros</a></li>
                    <li><a href="#" class="hover:text-blue-600">Carreras</a></li>
                    <li><a href="#" class="hover:text-blue-600">Blog</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Legal</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Privacidad</a></li>
                    <li><a href="#" class="hover:text-blue-600">Términos</a></li>
                    <li><a href="#" class="hover:text-blue-600">Cookies</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Síguenos</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Instagram</a></li>
                    <li><a href="#" class="hover:text-blue-600">Facebook</a></li>
                    <li><a href="#" class="hover:text-blue-600">Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-gray-500 mt-10 text-sm">
            © {{ date('Y') }} Sin Beca No Hay Renta. Todos los derechos reservados.
        </div>
    </footer>

<script>
    // Obtenemos referencias SOLO si el prompt existe en el HTML
    const promptElement = document.getElementById('preferences-prompt');
    const dontShowCheckbox = document.getElementById('dont-show-again');

    // Función global para cerrar el prompt
    function dismissPrompt() {
        // Solo intentamos guardar si el checkbox existe y está marcado
        if (dontShowCheckbox && dontShowCheckbox.checked) {
            localStorage.setItem('hidePreferencePrompt', 'true');
        }
        // Solo intentamos ocultar si el prompt existe
        if (promptElement) {
            promptElement.style.display = 'none';
        }
    }

    // Al cargar la página, revisamos localStorage SOLO si el prompt existe
    if (promptElement && localStorage.getItem('hidePreferencePrompt') === 'true') {
        // Comentado para la prueba, descomenta si quieres ocultar al inicio basado en localStorage
        promptElement.style.display = 'none';
        // console.log("LocalStorage flag detected, prompt should be hidden.");
    } else if (promptElement) {
        // console.log("LocalStorage flag not found or prompt doesn't exist initially.");
    }
</script>
        {{-- ===================== LOGIN MODAL ===================== --}}
        {{-- Hidden by default using Tailwind classes --}}
        <div id="loginModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md relative">
                {{-- Close Button --}}
                <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mb-6 text-center">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Iniciar Sesión</h3>
                </div>

                {{-- Login Form (Uses Breeze's route) --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input id="email" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                        <input id="password" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm" type="password" name="password" required autocomplete="current-password" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
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
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}" class="underline hover:text-blue-600 dark:hover:text-blue-400">
                            Regístrate aquí
                        </a>
                    </div>
                </form>
            </div>
        </div>
        {{-- =================== END LOGIN MODAL =================== --}}
        {{-- Just before </body> in welcome.blade.php --}}
        <script>
            const loginModal = document.getElementById('loginModal');

            function openLoginModal() {
                if (loginModal) {
                    loginModal.classList.remove('hidden'); // Muestra el modal
                }
            }

            function closeLoginModal() {
                if (loginModal) {
                    loginModal.classList.add('hidden'); // Oculta el modal
                }
            }

            // Opcional: Cerrar el modal si se hace clic fuera de él
            window.addEventListener('click', function(event) {
                if (event.target === loginModal) {
                    closeLoginModal();
                }
            });
        </script>
</body>

</html>
