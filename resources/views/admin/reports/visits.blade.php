<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reporte de Visitas</title>

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

<body
  class="bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200
         dark:bg-gradient-to-b dark:from-slate-950 dark:via-slate-900 dark:to-slate-950
         text-gray-800 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-300">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  {{-- Botón Tema (mismo que en otras vistas) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
      <i id="theme-toggle-icon" class="fa-solid"></i>
      <span class="text-sm font-medium"></span>
  </button>

  <main class="max-w-7xl mx-auto px-4 lg:px-6 py-10 lg:py-12 space-y-10 flex-1">

    {{-- TÍTULO + DESCRIPCIÓN --}}
    <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
      <div>
        <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-3">
          <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700
                       dark:bg-blue-500/10 dark:text-blue-300">
            <i class="fa-solid fa-person-walking"></i>
          </span>
          <span>Reporte de Visitas</span>
        </h1>
        <p class="mt-2 text-sm md:text-base text-gray-600 dark:text-gray-400">
          Visualiza el comportamiento de tus visitas, el desempeño de los agentes y las próximas citas agendadas.
        </p>
      </div>
    </header>

    {{-- ========== RESUMEN GENERAL ========== --}}
    <section>
      <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i class="fa-solid fa-chart-pie text-blue-600 dark:text-blue-400"></i>
        <span>Resumen general</span>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Visitas registradas --}}
        <div class="p-5 bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                    rounded-2xl shadow-sm flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">
              Visitas registradas
            </p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-slate-100">
              {{ $totalVisits }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Históricas en el sistema
            </p>
          </div>
          <span class="inline-flex items-center justify-center w-12 h-12 rounded-full
                       bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300">
            <i class="fa-solid fa-calendar-check text-lg"></i>
          </span>
        </div>

        {{-- Estados diferentes --}}
        <div class="p-5 bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                    rounded-2xl shadow-sm flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">
              Estados diferentes
            </p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-slate-100">
              {{ $visitsByStatus->count() }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pendiente, confirmada, completada, cancelada…
            </p>
          </div>
          <span class="inline-flex items-center justify-center w-12 h-12 rounded-full
                       bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300">
            <i class="fa-solid fa-signal text-lg"></i>
          </span>
        </div>

        {{-- Próximas visitas --}}
        <div class="p-5 bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                    rounded-2xl shadow-sm flex items-center justify-between gap-4">
          <div>
            <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">
              Próximas visitas agendadas
            </p>
            <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-slate-100">
              {{ $upcomingVisitsCount }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Citas futuras en calendario
            </p>
          </div>
          <span class="inline-flex items-center justify-center w-12 h-12 rounded-full
                       bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300">
            <i class="fa-solid fa-clock text-lg"></i>
          </span>
        </div>
      </div>
    </section>

    {{-- ========== VISITAS POR ESTADO / AGENTE ========== --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      {{-- Por estado --}}
      <div class="space-y-3">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
          <i class="fa-solid fa-list-check text-indigo-500 dark:text-indigo-400"></i>
          <span>Visitas por estado</span>
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Distribución de visitas según su estado actual en el flujo.
        </p>

        <div class="overflow-hidden bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                    rounded-2xl shadow-sm">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-sm">
              <thead class="bg-gray-100/90 dark:bg-slate-800/90">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                    Estado
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                    Total
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse ($visitsByStatus as $status)
                  @php
                    $statusKey = $status->status ?? 'pendiente';
                    $statusColors = [
                        'pendi'      => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                        'pending'    => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                        'pendiente'  => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                        'confirmed'  => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                        'confirmada' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                        'completed'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
                        'completada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
                        'cancelled'  => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                        'cancelada'  => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                    ];
                    $statusLabels = [
                        'pendi'      => 'Pendiente',
                        'pending'    => 'Pendiente',
                        'pendiente'  => 'Pendiente',
                        'confirmed'  => 'Confirmada',
                        'confirmada' => 'Confirmada',
                        'completed'  => 'Completada',
                        'completada' => 'Completada',
                        'cancelled'  => 'Cancelada',
                        'cancelada'  => 'Cancelada',
                    ];
                    $statusClass = $statusColors[$statusKey] ?? 'bg-slate-100 text-slate-800 dark:bg-slate-500/20 dark:text-slate-200';
                    $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                  @endphp
                  <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/70">
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-200 font-medium">
                      {{ $status->total }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                      No hay visitas registradas para mostrar.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Por agente --}}
      <div class="space-y-3">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
          <i class="fa-solid fa-user-tie text-emerald-500 dark:text-emerald-400"></i>
          <span>Visitas por agente</span>
        </h2>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Revisa cuántas visitas ha atendido cada agente.
        </p>

        <div class="overflow-hidden bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                    rounded-2xl shadow-sm">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-sm">
              <thead class="bg-gray-100/90 dark:bg-slate-800/90">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                    Agente
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                    Visitas atendidas
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                @forelse ($visitsByAgent as $agent)
                  <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/70">
                    <td class="px-6 py-4 text-gray-800 dark:text-slate-100 font-medium whitespace-nowrap">
                      {{ $agent->agent?->name ?? 'Sin asignar' }}
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-200">
                      {{ $agent->total }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                      No hay visitas asociadas a agentes para mostrar.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    {{-- ========== EVOLUCIÓN MENSUAL ========== --}}
    <section class="space-y-3">
      <h2 class="text-lg font-semibold mb-1 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i class="fa-solid fa-chart-line text-fuchsia-500 dark:text-fuchsia-400"></i>
        <span>Evolución mensual</span>
      </h2>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Tendencia de visitas registradas mes a mes.
      </p>

      <div class="overflow-hidden bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                  rounded-2xl shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-sm">
            <thead class="bg-gray-100/90 dark:bg-slate-800/90">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Mes
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Total de visitas
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
              @forelse ($visitsByMonth as $month)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/70">
                  <td class="px-6 py-4 text-gray-800 dark:text-slate-100 font-medium whitespace-nowrap">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}
                  </td>
                  <td class="px-6 py-4 text-gray-700 dark:text-gray-200">
                    {{ $month->total }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    Aún no hay visitas registradas en el sistema.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    {{-- ========== PRÓXIMAS VISITAS ========== --}}
    <section class="space-y-3">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <div>
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-sky-500 dark:text-sky-400"></i>
            <span>Próximas visitas</span>
          </h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            Lista de citas futuras para dar seguimiento oportuno.
          </p>
        </div>
      </div>

      <div class="overflow-hidden bg-white/95 dark:bg-slate-900/95 border border-gray-200/80 dark:border-slate-800
                  rounded-2xl shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800 text-sm">
            <thead class="bg-gray-100/90 dark:bg-slate-800/90">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Fecha
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Propiedad
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Agente
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                  Estado
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
              @forelse ($upcomingVisits as $visit)
                @php
                  $statusKey = $visit->status ?? 'pendiente';
                  $statusColors = [
                      'pending'    => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                      'pendiente'  => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                      'confirmed'  => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                      'confirmada' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                      'completed'  => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
                      'completada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200',
                      'cancelled'  => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                      'cancelada'  => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                  ];
                  $statusLabels = [
                      'pending'    => 'Pendiente',
                      'pendiente'  => 'Pendiente',
                      'confirmed'  => 'Confirmada',
                      'confirmada' => 'Confirmada',
                      'completed'  => 'Completada',
                      'completada' => 'Completada',
                      'cancelled'  => 'Cancelada',
                      'cancelada'  => 'Cancelada',
                  ];
                  $statusClass = $statusColors[$statusKey] ?? 'bg-slate-100 text-slate-800 dark:bg-slate-500/20 dark:text-slate-200';
                  $statusLabel = $statusLabels[$statusKey] ?? ucfirst($statusKey);
                @endphp
                <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/70">
                  <td class="px-6 py-4 text-gray-800 dark:text-slate-100 font-medium whitespace-nowrap">
                    {{ $visit->visit_date?->format('d M Y H:i') }}
                  </td>
                  <td class="px-6 py-4 text-gray-700 dark:text-gray-200 max-w-xs">
                    <span class="line-clamp-2">
                      {{ $visit->property?->title ?? 'Sin propiedad' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-gray-700 dark:text-gray-200 whitespace-nowrap">
                    {{ $visit->agent?->name ?? 'Sin agente' }}
                  </td>
                  <td class="px-6 py-4 text-gray-700 dark:text-gray-200 whitespace-nowrap">
                    {{ $visit->client?->name ?? 'Sin cliente' }}
                  </td>
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium capitalize {{ $statusClass }}">
                      {{ $statusLabel }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    No hay visitas próximas agendadas.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

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
