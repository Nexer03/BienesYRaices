<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Agente - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <!-- Header simplificado -->
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                <i class="fas fa-home mr-2"></i>
                Sin beca<span class="text-gray-700"> no hay renta </span>
            </div>
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                <!-- SOLO DEJAMOS ESTOS DOS ENLACES -->
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Modo Visitante</a>
                <a href="{{ url('/dashboard') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Sección de bienvenida -->
        <section class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Bienvenido a la vista de agente
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Gestiona tus propiedades, agendas y comunicaciones desde un solo lugar
            </p>
        </section>

        <!-- Botón Crear Nueva Propiedad -->
        <div class="text-center mb-12">
            <a href="{{ route('properties.create') }}"
               class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-6 py-3 transition-colors duration-200 shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Crear Nueva Propiedad</span>
            </a>
        </div>

        <!-- Grid de acciones -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Mis Propiedades -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Mis Propiedades</h3>
                <p class="text-gray-600 mb-4">Gestiona y visualiza todas tus propiedades listadas</p>
                <a href="{{ route('properties.my') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver propiedades →
                </a>
            </div>

            <!-- Agendas -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Agendas</h3>
                <p class="text-gray-600 mb-4">Administra tus citas y visitas programadas</p>
                <a href="{{ route('agent.visits.index') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver agenda →
                </a>
            </div>

            <!-- Mensajería -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Mensajería</h3>
                <p class="text-gray-600 mb-4">Comunícate con clientes y prospectos</p>
                <a href="#"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Abrir mensajería →
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo y descripción -->
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

                <!-- Enlaces rápidos -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Enlaces rápidos</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('properties.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mis Propiedades
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('agent.visits.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Agendas
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mensajería
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('properties.create') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Nueva Propiedad
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contacto -->
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

            <!-- Línea divisoria -->
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
