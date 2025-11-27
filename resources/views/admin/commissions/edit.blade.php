<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Editar Comisión - Admin</title>

  {{-- Anti-flash --}}
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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* Force dark color scheme on date inputs in dark mode */
    html.dark input[type="date"] {
      color-scheme: dark;
    }
    
    /* Force invert the icon color for WebKit browsers */
    html.dark input[type="date"]::-webkit-calendar-picker-indicator {
      filter: invert(1) !important;
    }

    .theme-transition, .theme-transition * {
      transition: background-color 0.4s ease, color 0.4s ease, border-color 0.4s ease;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 flex flex-col">

  <x-main-header />

  {{-- Theme Toggle --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <main class="max-w-4xl mx-auto px-4 py-12 space-y-10 flex-grow">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <h2 class="text-3xl font-semibold text-slate-900 dark:text-slate-50 flex items-center gap-2">
        <i class="fa-solid fa-pen-to-square text-blue-500"></i> Editar Comisión
      </h2>
      <a href="{{ route('admin.commissions.index') }}"
         class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 font-medium flex items-center gap-1 transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Volver al listado
      </a>
    </div>

    {{-- Form --}}
    <section class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm p-6 md:p-8">
      
      <form method="POST" action="{{ route('admin.commissions.update', $commission) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Agente --}}
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Agente (opcional)
                </label>
                <select name="user_id" id="user_id"
                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 text-sm px-4 py-2.5
                               bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                    <option value="">Comisión general</option>
                    @foreach ($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(old('user_id', $commission->user_id) == $agent->id)>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo --}}
            <div>
                <label for="listing_type" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Tipo de operación
                </label>
                <select name="listing_type" id="listing_type" required
                        class="w-full rounded-lg border border-gray-300 dark:border-slate-700 text-sm px-4 py-2.5
                               bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                    <option value="sale" @selected(old('listing_type', $commission->listing_type) === 'sale')>Venta</option>
                    <option value="rent" @selected(old('listing_type', $commission->listing_type) === 'rent')>Renta</option>
                    <option value="both" @selected(old('listing_type', $commission->listing_type) === 'both')>Ambos</option>
                </select>
                @error('listing_type')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Porcentaje --}}
            <div>
                <label for="percentage" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Comisión del agente (%)
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" max="100"
                           name="percentage" id="percentage"
                           value="{{ old('percentage', $commission->percentage) }}" required
                           class="w-full rounded-lg border border-gray-300 dark:border-slate-700 text-sm px-4 py-2.5 pl-4
                                  bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">%</span>
                    </div>
                </div>
                @error('percentage')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Vigencia --}}
            <div>
                <label for="effective_from" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Vigencia desde
                </label>
                <input type="date" name="effective_from" id="effective_from"
                       value="{{ old('effective_from', optional($commission->effective_from)->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-gray-300 dark:border-slate-700 text-sm px-4 py-2.5
                              bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                @error('effective_from')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Notas --}}
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                Notas internas
            </label>
            <input type="text" name="notes" id="notes"
                   value="{{ old('notes', $commission->notes) }}"
                   placeholder="Ej. Comisión especial..."
                   class="w-full rounded-lg border border-gray-300 dark:border-slate-700 text-sm px-4 py-2.5
                          bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
            @error('notes')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-slate-800">
            <a href="{{ route('admin.commissions.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow-md
                           hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 transition-all transform hover:-translate-y-0.5">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Actualizar Comisión
            </button>
        </div>

      </form>
    </section>

  </main>

  <x-main-footer />

  {{-- Theme Logic --}}
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

      function apply(mode) {
        const isDark = mode === 'dark';
        html.classList.add('theme-transition');
        html.classList.toggle('dark', isDark);
        try { localStorage.setItem('theme', mode); } catch (e) {}
        setIconAndLabel();
        setTimeout(() => html.classList.remove('theme-transition'), 400);
      }

      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>

</body>
</html>
