<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Solicitudes de agentes — Sin beca no hay renta</title>

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

  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>

  <!-- Iconos -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
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
  <!-- HEADER GLOBAL -->
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

  <!-- CONTENIDO -->
  <main class="flex-1 max-w-7xl mx-auto px-6 py-10">
    <header class="mb-10 text-center">
      <h1 class="text-4xl font-extrabold text-slate-900 dark:text-slate-50">
        Gestión de solicitudes de agentes
      </h1>
      <p class="text-gray-500 dark:text-slate-400 mt-2">
        Aprueba, rechaza o revisa solicitudes pendientes con facilidad.
      </p>
    </header>

    {{-- Alertas --}}
    @foreach (['success', 'error', 'info'] as $statusKey)
      @if (session($statusKey))
        <div class="mb-6 px-4 py-3 rounded-lg shadow text-sm
          {{ $statusKey === 'success'
              ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-700/60'
              : ($statusKey === 'error'
                  ? 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-700/60'
                  : 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-700/60') }}">
          {{ session($statusKey) }}
        </div>
      @endif
    @endforeach

    {{-- Filtro --}}
    <section class="bg-white dark:bg-slate-900 shadow-md rounded-2xl p-6 mb-8 border border-gray-100 dark:border-slate-800">
      <form method="GET" action="{{ route('admin.agent-applications.index') }}" class="flex flex-col md:flex-row gap-4 md:items-end">
        <div class="flex-1">
          <label for="status"
                 class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-1">
            Filtrar por estado
          </label>
          <select id="status" name="status"
            class="w-full border border-gray-300 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-100
                   px-3 py-2 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="" @selected($status === '')>Todos</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_PENDING }}" @selected($status === \App\Models\AgentApplication::STATUS_PENDING)>Pendientes</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_APPROVED }}" @selected($status === \App\Models\AgentApplication::STATUS_APPROVED)>Aprobadas</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_REJECTED }}" @selected($status === \App\Models\AgentApplication::STATUS_REJECTED)>Rechazadas</option>
          </select>
        </div>
        <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
          Filtrar
        </button>
      </form>
    </section>

    {{-- Tabla --}}
    <section class="bg-white dark:bg-slate-900 shadow-lg rounded-2xl overflow-hidden border border-gray-100 dark:border-slate-800">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700 dark:text-slate-200">
          <thead class="bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 uppercase text-xs">
            <tr>
              <th class="px-6 py-3 font-semibold">Solicitante</th>
              <th class="px-6 py-3 font-semibold">RFC</th>
              <th class="px-6 py-3 font-semibold">CURP</th>
              <th class="px-6 py-3 font-semibold">Estado</th>
              <th class="px-6 py-3 font-semibold">Motivo rechazo</th>
              <th class="px-6 py-3 font-semibold text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @forelse ($applications as $application)
              <tr class="hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                <td class="px-6 py-4">
                  <div class="font-semibold text-gray-800 dark:text-slate-50">{{ $application->user->name }}</div>
                  <div class="text-gray-500 dark:text-slate-400 text-xs">{{ $application->user->email }}</div>
                </td>
                <td class="px-6 py-4">{{ $application->rfc }}</td>
                <td class="px-6 py-4">{{ $application->curp }}</td>
                <td class="px-6 py-4 capitalize">
                  @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                    <span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 px-2 py-1 rounded-full text-xs font-semibold">Pendiente</span>
                  @elseif ($application->status === \App\Models\AgentApplication::STATUS_APPROVED)
                    <span class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold">Aprobada</span>
                  @else
                    <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 px-2 py-1 rounded-full text-xs font-semibold">Rechazada</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300">
                  {{ $application->rejection_reason ?? '—' }}
                </td>
                <td class="px-6 py-4 text-right">

    <div class="flex flex-col items-stretch gap-3 w-full sm:w-64 ml-auto">

        {{-- Ver detalles --}} 
        <a href="{{ route('admin.agent-applications.show', $application) }}"
           class="inline-flex items-center justify-center gap-2 text-blue-400 hover:text-blue-300 text-sm font-medium transition">
            <i class="fa-solid fa-eye"></i>
            Ver detalles
        </a>

        @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)

            {{-- Botón Aprobar (moderno) --}} 
            <form method="POST" action="{{ route('admin.agent-applications.approve', $application) }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2
                           bg-emerald-600/90 hover:bg-emerald-500
                           text-white font-semibold text-sm
                           px-4 py-2 rounded-full shadow-md transition">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    Aprobar
                </button>
            </form>

            {{-- Rechazar con input --}} 
            <form method="POST" action="{{ route('admin.agent-applications.reject', $application) }}"
                  class="flex flex-col w-full gap-2">

                @csrf

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2
                           bg-red-600/90 hover:bg-red-500
                           text-white font-semibold text-sm
                           px-4 py-2 rounded-full shadow-md transition">
                    <i class="fa-solid fa-circle-xmark text-lg"></i>
                    Rechazar
                </button>

                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-comment text-red-400 pt-1"></i>
                    <textarea name="rejection_reason" required placeholder="Describe el motivo de rechazo" rows="2"
                        class="flex-1 px-3 py-2 text-sm rounded-xl border border-gray-300 dark:border-slate-700
                               bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100
                               focus:ring-2 focus:ring-red-400 transition shadow-sm"></textarea>
                </div>
            </form>

        @endif
    </div>

</td>

              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-6 text-gray-500 dark:text-slate-400">
                  No hay solicitudes registradas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="bg-gray-50 dark:bg-slate-900/70 border-t border-gray-200 dark:border-slate-800 px-6 py-4">
        {{ $applications->links() }}
      </div>
    </section>
  </main>

  <!-- FOOTER GLOBAL -->
  <x-main-footer />

  <script>
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

        // Quitar transición después de 400 ms para no afectar otras cosas
        setTimeout(() => {
          html.classList.remove('theme-transition');
        }, 400);
      }

      // Estado inicial (solo icono/texto)
      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>
</body>
</html>
