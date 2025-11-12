<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reporte de Ventas y Rentas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-7xl mx-auto px-4 py-12">
    <h2 class="text-3xl font-semibold text-gray-900 mb-8 flex items-center gap-2">
      <i class="fa-solid fa-chart-column text-blue-600"></i> Reporte de Ventas y Rentas
    </h2>

    {{-- ========== FILTROS ========== --}}
    @php
        $from   = $filters['from'] ? $filters['from']->format('Y-m-d') : '';
        $to     = $filters['to']   ? $filters['to']->format('Y-m-d')   : '';
        $agent  = $filters['agent'] ?? '';
        $city   = $filters['city']  ?? '';
    @endphp

    <section class="bg-white border border-gray-200 rounded-xl shadow mb-10">
      <div class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
          <i class="fa-solid fa-filter text-blue-600"></i> Filtros
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
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Desde</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Hasta</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Agente</label>
            <select name="agent"
                    class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Todos</option>
              @foreach($agentsOptions as $opt)
                <option value="{{ $opt->id }}" @selected($agent == $opt->id)>{{ $opt->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Ciudad</label>
            <select name="city"
                    class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Todas</option>
              @foreach($citiesOptions as $c)
                <option value="{{ $c }}" @selected($city === $c)>{{ $c }}</option>
              @endforeach
            </select>
          </div>

          <div class="md:col-span-12 flex items-center gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
              <i class="fa-solid fa-magnifying-glass"></i> Aplicar
            </button>
            <a href="{{ route('admin.reports.sales') }}"
               class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 transition">
              Limpiar
            </a>
          </div>
        </form>
      </div>
    </section>

    {{-- ========== KPIs ========== --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Propiedades vendidas</p>
        <p class="mt-2 text-2xl font-bold text-gray-800">{{ number_format($salesByAgent->sum('properties_count')) }}</p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Valor total vendido</p>
        <p class="mt-2 text-2xl font-bold text-emerald-600">
          ${{ number_format($totalValueSold, 2, '.', ',') }}
        </p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Reservas confirmadas</p>
        <p class="mt-2 text-2xl font-bold text-gray-800">{{ number_format($totalRentalReservations) }}</p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Ingresos por rentas</p>
        <p class="mt-2 text-2xl font-bold text-emerald-600">
          ${{ number_format($totalRentalRevenue, 2, '.', ',') }}
        </p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Comisiones ventas</p>
        <p class="mt-2 text-2xl font-bold text-indigo-600">
          ${{ number_format($salesCommissionTotal, 2, '.', ',') }}
        </p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Comisiones rentas</p>
        <p class="mt-2 text-2xl font-bold text-indigo-600">
          ${{ number_format($rentalCommissionTotal, 2, '.', ',') }}
        </p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Cargo cliente (ventas)</p>
        <p class="mt-2 text-2xl font-bold text-amber-600">
          ${{ number_format($salesCustomerChargeTotal, 2, '.', ',') }}
        </p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-xs font-semibold uppercase text-gray-500">Cargo cliente (rentas)</p>
        <p class="mt-2 text-2xl font-bold text-amber-600">
          ${{ number_format($rentalCustomerChargeTotal, 2, '.', ',') }}
        </p>
      </div>
    </section>

    {{-- ========== GRÁFICAS OPTIMIZADAS ========== --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Ventas por agente (MXN)</h4>
        <div class="h-52 flex items-center justify-center">
          <canvas id="salesByAgentChart" class="max-h-52 w-full"></canvas>
        </div>
      </div>

      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Rentas por agente (MXN)</h4>
        <div class="h-52 flex items-center justify-center">
          <canvas id="rentalsByAgentChart" class="max-h-52 w-full"></canvas>
        </div>
      </div>
    </section>

    {{-- ========== TABLAS Y COMPARATIVAS (mantén tu include o contenido anterior) ========== --}}
    @include('admin.reports.partials.tables')
  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

  {{-- CHARTS OPTIMIZADOS --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    const salesLabels  = @json($salesByAgent->pluck('name'));
    const salesTotals  = @json($salesByAgent->map(fn($a) => round((float)($a->total_sales_amount ?? 0), 2)));
    const rentalLabels = @json($rentalsByAgent->pluck('agent.name'));
    const rentalTotals = @json($rentalsByAgent->map(fn($r) => round((float)$r->total_revenue, 2)));
    const formatMoney = v => '$' + Number(v).toLocaleString();

    const makeChart = (ctx, label, labels, data, color) => {
      if (!labels.length || !data.some(v => v > 0)) {
        ctx.parentNode.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">Sin datos disponibles</p>';
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
          plugins: {
            legend: { display: false }
          }
        }
      });
    };

    makeChart(document.getElementById('salesByAgentChart'), 'Ventas (MXN)', salesLabels, salesTotals, '#2563EB');
    makeChart(document.getElementById('rentalsByAgentChart'), 'Rentas (MXN)', rentalLabels, rentalTotals, '#059669');
  </script>
</body>
</html>
