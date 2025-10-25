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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                <i class="fas fa-home mr-2"></i>
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
                <a href="{{ route('login') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Iniciar
                    sesión</a>
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
            <div class="relative group">
                <!-- Botón izquierdo - MÁS AFUERA -->
                <button class="carousel-prev absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-left text-gray-700 text-lg"></i>
                </button>
                
                <!-- Carrusel - CON MÁRGENES PARA LOS BOTONES -->
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

                <!-- Botón derecho - MÁS AFUERA -->
                <button class="carousel-next absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-6 bg-white shadow-lg rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-50 transition-all opacity-0 group-hover:opacity-100 z-10 border border-gray-200">
                    <i class="fas fa-chevron-right text-gray-700 text-lg"></i>
                </button>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
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
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Navegación</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mis Visitas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mapa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Panel de Agente
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('agent.view') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Modo Vendedor
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            soporte@sinbeca.com
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            +1 (555) 123-4567
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Ciudad, País
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-300 text-sm">
                        &copy; 2024 SIN BECA NO HAY RENTA. Todos los derechos reservados.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Privacidad
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Términos
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Cookies
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Funcionalidad para los carruseles
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los contenedores de carrusel
            const carouselContainers = document.querySelectorAll('.carousel-container');
            
            carouselContainers.forEach(container => {
                const prevBtn = container.parentElement.querySelector('.carousel-prev');
                const nextBtn = container.parentElement.querySelector('.carousel-next');
                
                // Calcular el ancho de scroll (aproximadamente 3-4 propiedades)
                const scrollAmount = 300;
                
                prevBtn.addEventListener('click', () => {
                    container.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });
                
                nextBtn.addEventListener('click', () => {
                    container.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>

</body>

</html>