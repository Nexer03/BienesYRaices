<x-app-layout> {{-- Asume que usas el layout app.blade.php --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestionar Todas las Propiedades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Éxito o Error --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-semibold mb-4">Lista Completa de Propiedades</h3>

                    {{-- FORMULARIO DE FILTRADO --}}
                    <form action="{{ route('admin.properties.index') }}" method="GET" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            {{-- Filtro por Agente --}}
                            <div>
                                <label for="agent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Agente</label>
                                <select name="agent_id" id="agent_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos los Agentes</option>
                                    {{-- Asume que $agents se pasa desde el controlador --}}
                                    @foreach ($agents ?? [] as $id => $name)
                                        <option value="{{ $id }}" @selected(request('agent_id') == $id)>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Placeholder para otros filtros --}}
                            {{-- <div> ... </div> --}}

                            {{-- Botones --}}
                            <div class="flex space-x-2 md:col-start-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Filtrar
                                </button>
                                <a href="{{ route('admin.properties.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 focus:outline-none">
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                    {{-- FIN FORMULARIO DE FILTRADO --}}

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Agente</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo (V/R)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($properties as $property)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $property->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $property->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $property->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${{ number_format($property->price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $property->listing_type == 'rent' ? 'Renta' : 'Venta' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $property->status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- BOTÓN ELIMINAR (llama a JS para modal) --}}
                                            <button type="button"
                                                    onclick="openAdminDeletePropertyModal(this)"
                                                    data-property-name="{{ $property->title }}"
                                                    data-delete-url="{{ route('admin.properties.destroy', $property) }}"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No hay propiedades que coincidan con los filtros.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $properties->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ================= DELETE CONFIRMATION MODAL (ADMIN PROPERTIES) ================= --}}
    <div id="deleteAdminPropertyConfirmModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4 modal">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md relative modal-content">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirmar Eliminación</h3>
            <p class="mb-6 text-gray-600 dark:text-gray-400">¿Estás seguro de que quieres eliminar la propiedad <strong id="deleteAdminPropertyName"></strong>? Esta acción no se puede deshacer.</p>

            <form id="deleteAdminPropertyConfirmForm" method="POST" action=""> {{-- Action set by JS --}}
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeAdminDeletePropertyModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar Propiedad</button>
                </div>
            </form>
            <button onclick="closeAdminDeletePropertyModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                &times;
             </button>
        </div>
    </div>
    {{-- =============== END DELETE CONFIRMATION MODAL (ADMIN PROPERTIES) =============== --}}

    {{-- Script para controlar el modal de eliminación --}}
    @push('scripts')
    <script>
        // Referencias al modal de eliminación de propiedades (Admin)
        const deleteAdminPropertyModal = document.getElementById('deleteAdminPropertyConfirmModal');
        const deleteAdminPropertyForm = document.getElementById('deleteAdminPropertyConfirmForm');
        const deleteAdminPropertyNameSpan = document.getElementById('deleteAdminPropertyName');

        function openAdminDeletePropertyModal(button) {
            const propertyName = button.dataset.propertyName;
            const deleteUrl = button.dataset.deleteUrl;

            // Rellenamos el modal con los datos correctos
            if (deleteAdminPropertyNameSpan) deleteAdminPropertyNameSpan.textContent = propertyName;
            if (deleteAdminPropertyForm) deleteAdminPropertyForm.action = deleteUrl;

            // Mostramos el modal
            if (deleteAdminPropertyModal) deleteAdminPropertyModal.classList.remove('hidden');
        }

        function closeAdminDeletePropertyModal() {
            // Ocultamos el modal
            if (deleteAdminPropertyModal) deleteAdminPropertyModal.classList.add('hidden');
        }

        // Cerrar modal si se hace clic fuera
        window.addEventListener('click', function(event) {
            if (event.target === deleteAdminPropertyModal) {
                closeAdminDeletePropertyModal();
            }
        });
    </script>
    @endpush

</x-app-layout>
