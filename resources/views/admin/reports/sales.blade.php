<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reporte de Ventas y Rentas</title>

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
    /* Scrollbar bonito para tablas */
    .table-wrapper::-webkit-scrollbar {
      height: 8px;
    }
    .table-wrapper::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 4px;
    }
    .table-wrapper::-webkit-scrollbar-thumb:hover {
      background: #9ca3af;
    }

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
      <i class="fa-solid fa-chart-column text-blue-600"></i>
      Reporte de Ventas y Rentas
    </h2>

    {{-- ======================================================
                        FILTROS
    ======================================================= --}}
    @php
        $from   = $filters['from'] ? $filters['from']->format('Y-m-d') : '';
        $to     = $filters['to']   ? $filters['to']->format('Y-m-d')   : '';
        $agent  = $filters['agent'] ?? '';
        $city   = $filters['city']  ?? '';
    @endphp

    <section class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow mb-10">
      <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
          <i class="fa-solid fa-filter text-blue-600"></i>
          Filtros
        </h3>

        <form method="GET" action="{{ route('admin.reports.properties.export') }}">
          <input type="hidden" name="from"  value="{{ $from }}">
          <input type="hidden" name="to"    value="{{ $to }}">
          <input type="hidden" name="agent" value="{{ $agent }}">
          <input type="hidden" name="city"  value="{{ $city }}">

          <button type="submit"
                  class="bg-emerald-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-emerald-700 transition">
            <i class="fa-solid fa-file-excel"></i> Exportar Excel
          </button>
        </form>
      </div>

      <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 mb-1">Desde</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 mb-1">Agente</label>
            <select name="agent"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Todos</option>
              @foreach($agentsOptions as $opt)
                <option value="{{ $opt->id }}" @selected($agent == $opt->id)>{{ $opt->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 dark:text-gray-300 mb-1">Ciudad</label>
            <select name="city"
                    class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Todas</option>
              @foreach($citiesOptions as $c)
                <option value="{{ $c }}" @selected($city === $c)>{{ $c }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-12 flex items-center gap-3 mt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
              <i class="fa-solid fa-magnifying-glass"></i> Aplicar
            </button>

            <a href="{{ route('admin.reports.sales') }}"
               class="bg-gray-200 text-gray-700 dark:bg-slate-800 dark:text-slate-100 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-slate-700 transition">
              Limpiar
            </a>
          </div>

        </form>
      </div>
    </section>


    {{-- ======================================================
                        KPIS (3 x 3)
    ======================================================= --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Propiedades vendidas</p>
        <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-slate-100">
          {{ number_format($salesByAgent->sum('properties_count')) }}
        </p>
      </div>

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Valor total vendido</p>
        <p class="mt-3 text-3xl font-bold text-emerald-600">
          ${{ number_format($totalValueSold, 2, '.', ',') }}
        </p>
      </div>

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Reservas confirmadas</p>
        <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-slate-100">
          {{ number_format($totalRentalReservations) }}
        </p>
      </div>

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Ingresos por rentas</p>
        <p class="mt-3 text-3xl font-bold text-emerald-600">
          ${{ number_format($totalRentalRevenue, 2, '.', ',') }}
        </p>
      </div>

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Comisiones ventas</p>
        <p class="mt-3 text-3xl font-bold text-indigo-600">
          ${{ number_format($salesCommissionTotal, 2, '.', ',') }}
        </p>
      </div>

      <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm hover:shadow-md transition">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Comisiones rentas</p>
        <p class="mt-3 text-3xl font-bold text-indigo-600">
          ${{ number_format($rentalCommissionTotal, 2, '.', ',') }}
        </p>
      </div>

    </section>


    {{-- ======================================================
                        GRÁFICAS
    ======================================================= --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">

      <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mb-3">Ventas por agente (MXN)</h4>
        <div class="h-52 flex items-center justify-center">
          <canvas id="salesByAgentChart" class="max-h-52 w-full"></canvas>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-sm">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-200 mb-3">Rentas por agente (MXN)</h4>
        <div class="h-52 flex items-center justify-center">
          <canvas id="rentalsByAgentChart" class="max-h-52 w-full"></canvas>
        </div>
      </div>

    </section>


    {{-- ======================================================
                        TABLAS (INCLUDE)
    ======================================================= --}}
    <div class="table-wrapper overflow-x-auto border border-gray-200 dark:border-slate-800 rounded-xl shadow-sm p-0 bg-white dark:bg-slate-900">
      @include('admin.reports.partials.tables')
    </div>

  </main>

  {{-- FOOTER --}}
  <x-main-footer />


  {{-- ======================================================
                        CHARTS SCRIPT
    ======================================================= --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    const salesLabels  = @json($salesByAgent->pluck('name'));
    const salesTotals  = @json($salesByAgent->map(fn($a) => round((float)($a->total_sales_amount ?? 0), 2)));
    const rentalLabels = @json($rentalsByAgent->pluck('agent.name'));
    const rentalTotals = @json($rentalsByAgent->map(fn($r) => round((float)$r->total_revenue, 2)));

    const formatMoney = v => '$' + Number(v).toLocaleString();

    const makeChart = (ctx, label, labels, data, color) => {
      if (!labels.length || !data.some(v => v > 0)) {
        ctx.parentNode.innerHTML = '<p class="text-gray-400 dark:text-slate-400 text-sm text-center py-8">Sin datos disponibles</p>';
        return;
      }

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label,
            data,
            backgroundColor: color,
            borderRadius: 4,
            barThickness: 'flex'
          }]
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { callback: formatMoney }
            }
          },
          plugins: { legend: { display: false } }
        }
      });
    };

    makeChart(document.getElementById('salesByAgentChart'), 'Ventas (MXN)', salesLabels, salesTotals, '#2563EB');
    makeChart(document.getElementById('rentalsByAgentChart'), 'Rentas (MXN)', rentalLabels, rentalTotals, '#059669');
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
