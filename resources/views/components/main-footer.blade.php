<footer class="bg-gray-900 text-gray-300 pt-12 pb-8 mt-auto border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
        {{-- Marca y descripción --}}
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-home text-blue-500 text-xl"></i>
                <span class="text-white font-extrabold text-2xl tracking-tight">SIN BECA <span class="text-gray-200 font-bold">NO HAY RENTA</span></span>
            </div>
            <p class="text-gray-400 leading-relaxed mb-5 text-sm md:text-base">
                Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades con sus futuros dueños de manera eficiente y profesional.
            </p>
            <div class="flex space-x-4 text-lg">
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-x-twitter"></i></a>
            </div>
        </div>

        {{-- Navegación --}}
        <div>
            <h3 class="text-white text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Navegación</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('visits.my') }}" class="hover:text-blue-400 transition-colors">Mis Visitas</a></li>
                <li><a href="{{ route('properties.map') }}" class="hover:text-blue-400 transition-colors">Mapa</a></li>
                <li><a href="{{ route('agent.home') }}" class="hover:text-blue-400 transition-colors">Panel de Agente</a></li>
                <li><a href="{{ route('agent.view') }}" class="hover:text-blue-400 transition-colors">Modo Vendedor</a></li>
            </ul>
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="text-white text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Contacto</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center"><i class="fas fa-envelope text-blue-400 mr-2"></i> soporte@sinbeca.com</li>
                <li class="flex items-center"><i class="fas fa-phone text-blue-400 mr-2"></i> +1 (555) 123-4567</li>
                <li class="flex items-center"><i class="fas fa-map-marker-alt text-blue-400 mr-2"></i> Ciudad, País</li>
            </ul>
        </div>
    </div>

    {{-- Línea inferior --}}
    <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-500">
        <div class="flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-6">
            <p>© {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-blue-400 transition-colors">Privacidad</a>
                <a href="#" class="hover:text-blue-400 transition-colors">Términos</a>
                <a href="#" class="hover:text-blue-400 transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
