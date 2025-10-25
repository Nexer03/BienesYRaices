<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Propiedades - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Header con título integrado -->
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-8">
                    <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                        <i class="fas fa-home mr-2"></i>
                        Sin beca<span class="text-gray-700"> no hay renta </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 border-l border-gray-300 pl-8">Mis Propiedades</h1>
                </div>
                <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                    <a href="{{ route('agent.home') }}" class="hover:text-blue-600 transition">Panel de Agente</a>
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Modo Comprador</a>
                    <a href="{{ url('/dashboard') }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Barra de acciones -->
        <div class="flex justify-between items-center mb-6">
            <!-- Filtros a la izquierda -->
            <div class="flex gap-3">
                <a href="{{ route('properties.index') }}" 
                   class="px-5 py-2 rounded-full border {{ !request('type') ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium">
                    Todas
                </a>
                <a href="{{ route('properties.index', ['type' => 'rent']) }}" 
                   class="px-5 py-2 rounded-full border {{ request('type') == 'rent' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium">
                    Solo Renta
                </a>
                <a href="{{ route('properties.index', ['type' => 'sale']) }}" 
                   class="px-5 py-2 rounded-full border {{ request('type') == 'sale' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium">
                    Solo Venta
                </a>
            </div>

            <!-- Botón añadir propiedad a la derecha -->
            <a href="{{ route('properties.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-5 py-2 transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>Añadir Nueva Propiedad</span>
            </a>
        </div>

        <!-- Mensaje de éxito -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Lista de Propiedades - CONTENIDO PROPORCIONAL Y ALINEADO -->
        <div class="space-y-5">
            @forelse ($properties as $property)
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-6">
                    <!-- Header de la propiedad - MEJOR ALINEADO -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start mb-5">
                        <!-- Título y precio - ocupan 8/12 -->
                        <div class="lg:col-span-8">
                            <div class="flex items-center gap-4 mb-3">
                                <h3 class="text-xl font-semibold text-gray-900 flex-1">
                                    <a href="{{ route('properties.show', $property) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $property->title }}
                                    </a>
                                </h3>
                            </div>
                            <p class="text-2xl font-bold text-blue-600">${{ number_format($property->price, 2) }}</p>
                        </div>

                        <!-- Estado y acciones - ocupan 4/12 y ALINEADOS -->
                        <div class="lg:col-span-4">
                            <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 items-start sm:items-center lg:items-start xl:items-center justify-end">
                                <!-- Estado -->
                                @if($property->listing_type == 'rent')
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded-full whitespace-nowrap w-full sm:w-auto text-center">
                                        Renta
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-sm font-medium px-4 py-2 rounded-full whitespace-nowrap w-full sm:w-auto text-center">
                                        Venta
                                    </span>
                                @endif

                                <!-- Acciones - ALINEADAS VERTICALMENTE -->
                                <div class="flex gap-2 w-full sm:w-auto justify-center sm:justify-start">
                                    <a href="{{ route('properties.edit', $property) }}" 
                                       class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-colors whitespace-nowrap font-medium flex-1 sm:flex-none justify-center">
                                        <i class="fas fa-edit"></i>
                                        <span class="hidden sm:inline">Editar</span>
                                    </a>
                                    <form action="{{ route('properties.destroy', $property) }}" method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta propiedad?');"
                                          class="flex-1 sm:flex-none">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg transition-colors whitespace-nowrap font-medium w-full justify-center">
                                            <i class="fas fa-trash"></i>
                                            <span class="hidden sm:inline">Eliminar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes - PROPORCIONAL AL ANCHO -->
                    <div class="mb-5">
                        <h4 class="font-semibold text-gray-900 mb-3">Imágenes</h4>
                        @if ($property->images->isNotEmpty())
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach ($property->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         alt="Imagen de {{ $property->title }}" 
                                         class="w-full h-32 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No hay imágenes para esta propiedad.</p>
                        @endif
                    </div>

                    <!-- Amenidades - PROPORCIONAL AL ANCHO -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Amenidades</h4>
                        @if ($property->amenities->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($property->amenities as $amenity)
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                        {{ $amenity->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No se especificaron amenidades.</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <i class="fas fa-home text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes propiedades</h3>
                <p class="text-gray-500 mb-4">Comienza añadiendo tu primera propiedad</p>
                <a href="{{ route('properties.create') }}" 
                   class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-5 py-3 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Añadir Primera Propiedad</span>
                </a>
            </div>
            @endforelse
        </div>
    </main>

    <!-- Footer Estandarizado -->
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
                            <a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Panel de Agente
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('properties.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mis Propiedades
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('agent.visits') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Agendas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Modo Comprador
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

</body>
</html>