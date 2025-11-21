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

<body class="bg-gray-50 text-gray-800 dark:bg-slate-950 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-300">

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

  <main class="max-w-7xl mx-auto px-4 py-12 space-y-10 flex-1">

    <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2 mb-4">
      <i class="fa-solid fa-person-walking text-blue-600"></i> Reporte de Visitas
    </h2>

    {{-- ========== RESUMEN GENERAL ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Resumen general</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Visitas registradas</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-100">{{ $totalVisits }}</p>
        </div>

        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Estados diferentes</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-100">{{ $visitsByStatus->count() }}</p>
        </div>

        <div class="p-4 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Próximas visitas agendadas</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-slate-100">{{ $upcomingVisitsCount }}</p>
        </div>
      </div>
    </section>

    {{-- ========== VISITAS POR ESTADO / AGENTE ========== --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      {{-- Por estado --}}
      <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Visitas por estado</h3>
        <div class="overflow-x-auto bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
            <thead class="bg-gray-100 dark:bg-slate-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
              @forelse ($visitsByStatus as $status)
                <tr>
                  <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">
                    {{ ucfirst(__($status->status ?? 'Sin estado')) }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $status->total }}</td>
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

      {{-- Por agente --}}
      <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Visitas por agente</h3>
        <div class="overflow-x-auto bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
            <thead class="bg-gray-100 dark:bg-slate-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Agente</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Visitas atendidas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
              @forelse ($visitsByAgent as $agent)
                <tr>
                  <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">
                    {{ $agent->agent?->name ?? 'Sin asignar' }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $agent->total }}</td>
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
    </section>

    {{-- ========== EVOLUCIÓN MENSUAL ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Evolución mensual</h3>
      <div class="overflow-x-auto bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
          <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Mes</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Total de visitas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
            @forelse ($visitsByMonth as $month)
              <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $month->total }}</td>
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
    </section>

    {{-- ========== PRÓXIMAS VISITAS ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Próximas visitas</h3>
      <div class="overflow-x-auto bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
          <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Propiedad</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Agente</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
            @forelse ($upcomingVisits as $visit)
              <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-slate-100">
                  {{ $visit->visit_date?->format('d M Y H:i') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                  {{ $visit->property?->title ?? 'Sin propiedad' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                  {{ $visit->agent?->name ?? 'Sin agente' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                  {{ $visit->client?->name ?? 'Sin cliente' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 capitalize">
                  {{ $visit->status ?? 'pendiente' }}
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
