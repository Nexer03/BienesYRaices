<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Comisiones del Sistema</title>

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

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* Hacer visible el icono del calendario en modo oscuro */
    html.dark input[type="date"]::-webkit-calendar-picker-indicator {
      filter: invert(1) brightness(1.3);
    }
    html.dark input[type="date"] {
      color-scheme: dark;
    }

    /* Transición SUAVE al cambiar de tema */
    .theme-transition,
    .theme-transition * {
      transition:
        background-color 0.4s ease,
        color 0.4s ease,
        border-color 0.4s ease;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  {{-- BOTÓN MODO OSCURO --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <main class="max-w-6xl mx-auto px-4 py-12 space-y-10">

    {{-- TÍTULO Y BOTÓN DE REFRESCAR --}}
    <div class="flex items-center justify-between">
      <h2 class="text-3xl font-semibold text-slate-900 dark:text-slate-50 flex items-center gap-2">
        <i class="fa-solid fa-percent text-blue-500"></i> Comisiones del Sistema
      </h2>
      <a href="{{ route('admin.commissions.index') }}"
         class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1">
        <i class="fa-solid fa-rotate-right"></i> Actualizar
      </a>
    </div>

    {{-- ALERTA DE ÉXITO --}}
    @if (session('success'))
      <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3 text-sm
                  dark:bg-green-900/20 dark:border-green-700/60 dark:text-green-300">
        {{ session('success') }}
      </div>
    @endif

    {{-- FORMULARIO NUEVA COMISIÓN --}}
    <section class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Registrar nueva comisión</h3>

      <form method="POST" action="{{ route('admin.commissions.store') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-5">
        @csrf

        {{-- AGENTE --}}
        <div>
          <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Agente (opcional)
          </label>
          <select name="user_id" id="user_id"
                  class="w-full rounded-lg border-gray-300 dark:border-slate-700 text-sm px-3 py-2
                         bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                         focus:ring-blue-500 focus:border-blue-500">
            <option value="">Comisión general</option>
            @foreach ($agents as $agent)
              <option value="{{ $agent->id }}" @selected(old('user_id') == $agent->id)>
                {{ $agent->name }}
              </option>
            @endforeach
          </select>
          @error('user_id')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- TIPO --}}
        <div>
          <label for="listing_type" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Tipo de operación
          </label>
          <select name="listing_type" id="listing_type" required
                  class="w-full rounded-lg border-gray-300 dark:border-slate-700 text-sm px-3 py-2
                         bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                         focus:ring-blue-500 focus:border-blue-500">
            <option value="sale" @selected(old('listing_type') === 'sale')>Venta</option>
            <option value="rent" @selected(old('listing_type') === 'rent')>Renta</option>
            <option value="both" @selected(old('listing_type') === 'both')>Ambos</option>
          </select>
          @error('listing_type')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- COMISIÓN AGENTE --}}
        <div>
          <label for="percentage" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Comisión del agente (%)
          </label>
          <input type="number" step="0.01" min="0" max="100"
                 name="percentage" id="percentage" value="{{ old('percentage') }}" required
                 class="w-full rounded-lg border-gray-300 dark:border-slate-700 text-sm px-3 py-2
                        bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                        focus:ring-blue-500 focus:border-blue-500">
          @error('percentage')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- FECHA VIGENCIA --}}
        <div>
          <label for="effective_from" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Vigencia desde
          </label>
          <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from') }}"
                 class="w-full rounded-lg border-gray-300 dark:border-slate-700 text-sm px-3 py-2
                        bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                        focus:ring-blue-500 focus:border-blue-500">
          @error('effective_from')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- NOTAS --}}
        <div class="md:col-span-3">
          <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Notas internas
          </label>
          <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                 placeholder="Ej. Comisión preferencial por rendimiento"
                 class="w-full rounded-lg border-gray-300 dark:border-slate-700 text-sm px-3 py-2
                        bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                        focus:ring-blue-500 focus:border-blue-500">
          @error('notes')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- BOTÓN --}}
        <div class="md:col-span-2 flex items-end">
          <button type="submit"
                  class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
          </button>
        </div>
      </form>
    </section>

    {{-- TABLA DE COMISIONES --}}
    <section class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4">Comisiones registradas</h3>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
          <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Tipo</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Agente</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Comisión agente</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Vigente desde</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Notas</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
            @forelse ($commissions as $commission)
              <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                <td class="px-4 py-3 text-sm text-gray-800 dark:text-slate-100">
                  {{ __(match($commission->listing_type) {
                      'sale' => 'Venta',
                      'rent' => 'Renta',
                      default => 'Ambos',
                  }) }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-slate-200">
                  {{ $commission->agent?->name ?? 'General del sistema' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-slate-200">
                  {{ number_format($commission->percentage, 2) }}%
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-slate-200">
                  {{ optional($commission->effective_from)->format('d/m/Y') ?? '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-slate-200">
                  {{ $commission->notes ?: '—' }}
                </td>
                <td class="px-4 py-3 text-sm text-right">
                  <div class="inline-flex gap-3">
                    <a href="{{ route('admin.commissions.edit', $commission) }}"
                       class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                       <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form method="POST"
                          action="{{ route('admin.commissions.destroy', $commission) }}"
                          class="delete-commission-form">
                      @csrf
                      @method('DELETE')
                      <button type="button"
                              class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-medium btn-delete-commission">
                              <i class="fa-solid fa-trash-can"></i> Eliminar
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-slate-400">
                  Aún no hay comisiones configuradas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Configuración de SweetAlert2 con estilos de Tailwind (Dark/Light)
      const swalOpts = {
        buttonsStyling: false,
        reverseButtons: true,
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar',
        customClass: {
          popup: 'rounded-xl bg-white text-gray-900 border border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700',
          title: 'text-gray-900 dark:text-gray-100',
          htmlContainer: 'text-gray-700 dark:text-gray-300',
          actions: 'gap-3',
          confirmButton: 'px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none',
          cancelButton: 'px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 focus:outline-none dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600',
        },
      };

      // Manejo del botón eliminar
      document.querySelectorAll('.btn-delete-commission').forEach(btn => {
        btn.addEventListener('click', function () {
          const form = this.closest('.delete-commission-form');
          
          Swal.fire({
            ...swalOpts,
            icon: 'warning',
            title: '¿Eliminar comisión?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          });
        });
      });
    });

    // Toggle de modo oscuro con transición suave
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

      function apply(mode) {
        const isDark = mode === 'dark';

        // Activar clase de transición
        html.classList.add('theme-transition');

        html.classList.toggle('dark', isDark);
        try { localStorage.setItem('theme', mode); } catch (e) {}

        setIconAndLabel();

        // Quitar transición después de 400 ms
        setTimeout(() => {
          html.classList.remove('theme-transition');
        }, 400);
      }

      // Estado inicial
      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>

</body>
</html>
