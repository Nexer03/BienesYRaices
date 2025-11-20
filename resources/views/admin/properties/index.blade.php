<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestionar Propiedades</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-semibold text-gray-900 mb-8 flex items-center gap-2">
      <i class="fa-solid fa-building text-blue-600"></i> Gestión de Propiedades
    </h2>

    {{-- Mensajes de éxito / error --}}
    @if(session('success'))
      <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg shadow-sm">
        {{ session('error') }}
      </div>
    @endif

    {{-- BOTONES SUPERIORES --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-8">
      <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-list"></i> Lista Completa de Propiedades
      </h3>
      <div class="flex gap-3">
        <a href="{{ route('admin.reports.sales') }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
          <i class="fa-solid fa-chart-line"></i> Reporte de ventas
        </a>
        <a href="{{ route('admin.reports.visits') }}" class="bg-emerald-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-emerald-700 transition">
          <i class="fa-solid fa-eye"></i> Reporte de visitas
        </a>
      </div>
    </div>

    {{-- FORMULARIO FILTRO --}}
    <div class="bg-white rounded-xl shadow p-6 mb-10 border border-gray-200">
      <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-filter text-blue-600"></i> Filtrar Propiedades
      </h4>
      <form action="{{ route('admin.properties.index') }}" method="GET" class="grid md:grid-cols-4 gap-4">
        <div>
          <label for="agent_id" class="block font-medium mb-1">Agente</label>
          <select name="agent_id" id="agent_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Todos los agentes</option>
            @foreach ($agents ?? [] as $id => $name)
              <option value="{{ $id }}" @selected(request('agent_id') == $id)>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="flex gap-3 items-end md:col-start-4">
          <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
          </button>
          <a href="{{ route('admin.properties.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 transition">
            Limpiar
          </a>
        </div>
      </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
        <thead class="bg-blue-50">
          <tr>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">ID</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Título</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Agente</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Precio</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Tipo (V/R)</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Estado</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          @forelse ($properties as $property)
            <tr class="hover:bg-gray-50 transition">
              <td class="px-6 py-3 font-medium text-gray-900">{{ $property->id }}</td>
              <td class="px-6 py-3">{{ $property->title }}</td>
              <td class="px-6 py-3">{{ $property->user->name ?? 'N/A' }}</td>
              <td class="px-6 py-3">${{ number_format($property->price, 2) }}</td>
              <td class="px-6 py-3">{{ $property->listing_type == 'rent' ? 'Renta' : 'Venta' }}</td>
              <td class="px-6 py-3">{{ $property->status_label }}</td>

              <td class="px-6 py-3">
                <button type="button"
                        onclick="openAdminDeletePropertyModal(this)"
                        data-property-name="{{ $property->title }}"
                        data-delete-url="{{ route('admin.properties.destroy', $property) }}"
                        class="text-red-600 hover:underline font-medium">
                  <i class="fa-solid fa-trash-can"></i> Eliminar
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                No hay propiedades que coincidan con los filtros.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-6">
      {{ $properties->links() }}
    </div>
  </main>

  {{-- MODAL ELIMINAR --}}
  <div id="deleteAdminPropertyConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 w-full max-w-md shadow-xl relative">
      <button onclick="closeAdminDeletePropertyModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="text-xl font-semibold mb-4 text-red-600 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación
      </h3>
      <p class="text-gray-700 mb-6">
        ¿Estás seguro de eliminar la propiedad <strong id="deleteAdminPropertyName"></strong>? Esta acción no se puede deshacer.
      </p>

      <form id="deleteAdminPropertyConfirmForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-3">
          <button type="button" onclick="closeAdminDeletePropertyModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Eliminar Propiedad
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- FOOTER --}}
  <x-main-footer />

  <script>
    const deleteAdminPropertyModal = document.getElementById('deleteAdminPropertyConfirmModal');
    const deleteAdminPropertyForm = document.getElementById('deleteAdminPropertyConfirmForm');
    const deleteAdminPropertyNameSpan = document.getElementById('deleteAdminPropertyName');

    function openAdminDeletePropertyModal(button) {
      const propertyName = button.dataset.propertyName;
      const deleteUrl = button.dataset.deleteUrl;
      if (deleteAdminPropertyNameSpan) deleteAdminPropertyNameSpan.textContent = propertyName;
      if (deleteAdminPropertyForm) deleteAdminPropertyForm.action = deleteUrl;
      if (deleteAdminPropertyModal) deleteAdminPropertyModal.classList.remove('hidden');
    }

    function closeAdminDeletePropertyModal() {
      if (deleteAdminPropertyModal) deleteAdminPropertyModal.classList.add('hidden');
    }

    window.addEventListener('click', function(event) {
      if (event.target === deleteAdminPropertyModal) closeAdminDeletePropertyModal();
    });
  </script>
</body>
</html>
