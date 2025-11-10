<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- Título dinámico según el rol --}}
            @if (Auth::user()->role === 'admin')
                {{ __('Panel de Administrador') }}
            @else
                {{ __('Panel Principal') }}
            @endif
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Mensaje de Bienvenida (visible para todos) --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                ¡Bienvenido de nuevo, {{ Auth::user()->name }}! 👋
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- ==        CONTENIDO ESPECÍFICO DE ADMIN        == --}}
        {{-- ============================================= --}}
        @if (Auth::user()->role === 'admin')
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Herramientas de Administración</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    {{-- Tarjeta: Gestionar Propiedades --}}
                    <a href="{{ route('admin.properties.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Gestionar Todas las Propiedades</h4>
                        <p class="text-gray-600 dark:text-gray-400">Ver, filtrar y eliminar cualquier propiedad del sistema.</p>
                    </a>

                    {{-- Tarjeta: Gestionar Usuarios --}}
                    <a href="{{ route('admin.users.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Gestionar Usuarios</h4>
                        <p class="text-gray-600 dark:text-gray-400">Ver, editar, filtrar y eliminar usuarios registrados.</p>
                    </a>
                    {{-- Tarjeta: Solicitudes de Agentes --}}
                    <a href="{{ route('admin.agent-applications.index') }}"
                    class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Solicitudes de Agentes</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Revisar, aprobar o rechazar solicitudes de usuarios que desean convertirse en agentes.
                        </p>
                    </a>
                    {{-- Tarjeta: Reportes (Ejemplo HU-05, HU-12) --}}
                    <a href="{{ route('admin.reports.sales') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Reportes de Ventas/Rentas</h4>
                        <p class="text-gray-600 dark:text-gray-400">Analizar desempeño y comparar propiedades por zona.</p>
                    </a>

                    {{-- Tarjeta: Reporte de Visitas --}}
                    <a href="{{ route('admin.reports.visits') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Reporte de Visitas</h4>
                        <p class="text-gray-600 dark:text-gray-400">Revisa la demanda y seguimiento de visitas agendadas.</p>
                    </a>

                    {{-- Tarjeta: Comisiones (Ejemplo HU-09) --}}
                    <a href="#" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Control de Comisiones</h4>
                        <p class="text-gray-600 dark:text-gray-400">Calcular y revisar las comisiones de los agentes.</p>
                    </a>

                     {{-- Tarjeta: Exportar (Ejemplo HU-15) --}}
                    <a href="#" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Exportar Reportes</h4>
                        <p class="text-gray-600 dark:text-gray-400">Descargar datos de propiedades en Excel.</p>
                    </a>


                </div>
            </div>
            <hr class="my-6 border-gray-300 dark:border-gray-700">
        @endif
        {{-- ============================================= --}}

        {{-- Acciones Comunes (visibles para todos, excepto si eres admin y ya las tienes arriba) --}}
        @if (Auth::user()->role !== 'admin')
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Acciones Rápidas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Tarjeta: Mis Propiedades --}}
                  <a href="{{ route('properties.my') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Mis Propiedades</h4>
                    <p class="text-gray-600 dark:text-gray-400">Ver y gestionar tus propiedades.</p>
                </a>
                {{-- Tarjeta: Añadir Propiedad --}}
                <a href="{{ route('properties.create') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                   <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Añadir Propiedad</h4>
                   <p class="text-gray-600 dark:text-gray-400">Publica una nueva propiedad.</p>
                </a>
                 {{-- Tarjeta: Mis Preferencias --}}
                 <a href="{{ route('preferences.edit') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Mis Preferencias</h4>
                    <p class="text-gray-600 dark:text-gray-400">Ajusta tus criterios de búsqueda.</p>
                </a>
                {{-- Tarjeta: Editar Perfil (Común) --}}
                <a href="{{ route('profile.edit') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                   <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Editar Perfil</h4>
                   <p class="text-gray-600 dark:text-gray-400">Actualiza tus datos personales.</p>
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
