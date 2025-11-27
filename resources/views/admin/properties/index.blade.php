<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestionar Propiedades</title>

  {{-- Anti-flash: aplica tema guardado ANTES de pintar la página --}}
  <script>
      (function () {
          try {
              if (localStorage.getItem('theme') === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          } catch (e) {
              document.documentElement.classList.remove('dark');
          }
      })();
  </script>

  {{-- Tailwind --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
      tailwind.config = {
          darkMode: 'class'
      };
  </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
      /* Fade suave para todo al cambiar de tema */
      html.theme-fade * {
          transition:
              background-color .35s ease,
              color .35s ease,
              border-color .35s ease,
              fill .35s ease;
      }

      /* Botón de tema: animación */
      #theme-toggle {
          transition: background-color .25s ease,
                      color .25s ease,
                      transform .25s ease,
                      box-shadow .25s ease;
      }

      #theme-toggle.theme-bounce {
          transform: translateY(-1px) scale(1.03);
          box-shadow: 0 15px 30px rgba(0,0,0,.18);
      }

      #theme-toggle-icon {
          transition: transform .35s ease, opacity .2s ease;
      }

      #theme-toggle-icon.theme-spin {
          transform: rotate(180deg);
      }

      .dark footer {
          background-color: #020617 !important; /* slate-950 */
      }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-300">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  {{-- Botón Tema (igual que en home) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
      <i id="theme-toggle-icon" class="fa-solid"></i>
      <span class="text-sm font-medium"></span>
  </button>

  <main class="max-w-7xl mx-auto px-4 py-12 flex-1">
    <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100 mb-8 flex items-center gap-2">
      <i class="fa-solid fa-building text-blue-600"></i> Gestión de Propiedades
    </h2>

    {{-- Mensajes de éxito / error --}}
    @if(session('success'))
      <div class="mb-6 bg-green-100 border border-green-300 text-green-800 dark:bg-emerald-900/40 dark:border-emerald-700 dark:text-emerald-200 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-6 bg-red-100 border border-red-300 text-red-800 dark:bg-red-900/40 dark:border-red-700 dark:text-red-200 px-4 py-3 rounded-lg shadow-sm">
        {{ session('error') }}
      </div>
    @endif

    {{-- BOTONES SUPERIORES --}}
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-8">
      <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
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
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-6 mb-10 border border-gray-200 dark:border-slate-800">
      <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-filter text-blue-600"></i> Filtrar Propiedades
      </h4>
      <form action="{{ route('admin.properties.index') }}" method="GET" class="grid md:grid-cols-4 gap-4">
        <div>
          <label for="agent_id" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Agente</label>
          <select name="agent_id" id="agent_id"
                  class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Todos los agentes</option>
            @foreach ($agents ?? [] as $id => $name)
              <option value="{{ $id }}" @selected(request('agent_id') == $id)>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="flex gap-3 items-end md:col-start-4">
          <button type="submit"
                  class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
          </button>
          <a href="{{ route('admin.properties.index') }}"
             class="bg-gray-200 text-gray-700 dark:bg-slate-800 dark:text-gray-100 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-slate-700 transition">
            Limpiar
          </a>
        </div>
      </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-slate-800">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700 text-sm text-gray-700 dark:text-slate-200">
        <thead class="bg-blue-50 dark:bg-slate-800">
          <tr>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">ID</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Título</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Agente</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Precio</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Tipo (V/R)</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Estado</th>
            <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-slate-200 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
          @forelse ($properties as $property)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition">
              <td class="px-6 py-3 font-medium text-gray-900 dark:text-slate-100">{{ $property->id }}</td>
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
              <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-slate-300">
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
    <div class="bg-white dark:bg-slate-900 rounded-xl p-8 w-full max-w-md shadow-xl relative">
      <button onclick="closeAdminDeletePropertyModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="text-xl font-semibold mb-4 text-red-600 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación
      </h3>
      <p class="text-gray-700 dark:text-slate-200 mb-6">
        ¿Estás seguro de eliminar la propiedad <strong id="deleteAdminPropertyName"></strong>? Esta acción no se puede deshacer.
      </p>

      <form id="deleteAdminPropertyConfirmForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-3">
          <button type="button"
                  onclick="closeAdminDeletePropertyModal()"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-slate-800 dark:text-gray-100 dark:hover:bg-slate-700">
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

  {{-- Lógica del botón de tema --}}
  <script>
      (function () {
          const html  = document.documentElement;
          const btn   = document.getElementById('theme-toggle');
          const icon  = document.getElementById('theme-toggle-icon');
          const label = btn?.querySelector('span');

          function setIconAndLabel() {
              const isDark = html.classList.contains('dark');
              if (!icon || !label) return;

              icon.classList.remove('fa-sun', 'fa-moon');
              icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
              label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
          }

          function startPageFade() {
              html.classList.add('theme-fade');
              setTimeout(() => html.classList.remove('theme-fade'), 400);
          }

          function animateButton() {
              if (!btn || !icon) return;
              btn.classList.add('theme-bounce');
              icon.classList.add('theme-spin');
              setTimeout(() => {
                  btn.classList.remove('theme-bounce');
                  icon.classList.remove('theme-spin');
              }, 350);
          }

          function apply(mode) {
              const isDark = mode === 'dark';
              startPageFade();
              html.classList.toggle('dark', isDark);
              try {
                  localStorage.setItem('theme', mode);
              } catch (e) {}
              setIconAndLabel();
              animateButton();
          }

          // Estado inicial de icono/texto según clase actual del <html>
          setIconAndLabel();

          btn?.addEventListener('click', () => {
              const next = html.classList.contains('dark') ? 'light' : 'dark';
              apply(next);
          });
      })();
  </script>
</body>
</html>
