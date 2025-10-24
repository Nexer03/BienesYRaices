<x-app-layout>
    {{-- El slot 'header' define el título que se mostrará en el layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel Principal') }}
        </h2>
    </x-slot>

    {{-- Contenido principal del dashboard --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Mensaje de Bienvenida --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                ¡Bienvenido de nuevo, {{ Auth::user()->name }}! 👋
            </div>
        </div>

        {{-- Alerta de Preferencias (Opcional, si quieres recordarlo aquí también) --}}
        @if (is_null(Auth::user()->preferences))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p class="font-bold">¡Personaliza tu experiencia!</p>
                <p>Aún no has definido tus <a href="{{ route('preferences.edit') }}" class="underline hover:text-yellow-800">preferencias de búsqueda</a>.</p>
            </div>
        @endif

        {{-- Sección de Acciones Rápidas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Tarjeta: Mis Propiedades --}}
            <a href="{{ route('properties.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Mis Propiedades</h3>
                <p class="text-gray-600 dark:text-gray-400">Ver, editar o eliminar tus propiedades publicadas.</p>
            </a>

            {{-- Tarjeta: Añadir Propiedad --}}
            <a href="{{ route('properties.create') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Añadir Propiedad</h3>
                <p class="text-gray-600 dark:text-gray-400">Publica una nueva casa, departamento u oficina.</p>
            </a>

            {{-- Tarjeta: Editar Perfil --}}
            <a href="{{ route('profile.edit') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Editar Perfil</h3>
                <p class="text-gray-600 dark:text-gray-400">Actualiza tu nombre, email o contraseña.</p>
            </a>

            {{-- Tarjeta: Editar Preferencias --}}
             <a href="{{ route('preferences.edit') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Mis Preferencias</h3>
                <p class="text-gray-600 dark:text-gray-400">Ajusta tus criterios de búsqueda para mejores recomendaciones.</p>
            </a>

            {{-- Puedes añadir más tarjetas aquí según los roles o funcionalidades --}}

        </div>

    </div>
</x-app-layout>
