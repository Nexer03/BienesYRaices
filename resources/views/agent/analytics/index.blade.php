<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <title>Panel de estadísticas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <!-- CONTENIDO -->
  <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-12">
    <header class="mb-10">
      <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Estadísticas del Agente</h1>
      <p class="text-gray-500 text-lg">Monitorea tus propiedades, visitas y rendimiento general en un solo lugar.</p>
    </header>

    <!-- FILTROS -->
    <section class="bg-white/60 backdrop-blur-md shadow-md border border-gray-100 rounded-2xl p-6 mb-10">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtrar por fecha</h2>
      <form method="GET" class="grid md:grid-cols-4 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Desde</label>
          <input type="date" name="from" value="{{ optional($from)->toDateString() }}"
            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Hasta</label>
          <input type="date" name="to" value="{{ optional($to)->toDateString() }}"
            class="w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded-lg py-2 hover:bg-blue-700 transition">Aplicar</button>
        </div>
        <div class="flex items-end">
          <a href="{{ route('agent.analytics') }}" class="w-full text-center border border-gray-300 rounded-lg py-2 hover:bg-gray-100 transition">Limpiar</a>
        </div>
      </form>
    </section>

    <!-- RESUMEN DE MÉTRICAS -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-gradient-to-tr from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">Propiedades Totales</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $totalProps }}</h3>
      </div>
      <div class="bg-gradient-to-tr from-green-500 to-green-600 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">En Venta</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $saleProps }}</h3>
      </div>
      <div class="bg-gradient-to-tr from-indigo-500 to-indigo-600 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">En Renta</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $rentProps }}</h3>
      </div>
      <div class="bg-gradient-to-tr from-rose-500 to-pink-600 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">Visitas (90 días)</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $visits['total'] }}</h3>
      </div>
    </section>

    <!-- DISTRIBUCIÓN -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      @php
        $badges = [
          'pending' => ['Pendientes','bg-yellow-400/80 text-yellow-900'],
          'confirmed' => ['Confirmadas','bg-blue-400/80 text-blue-900'],
          'completed' => ['Completadas','bg-green-400/80 text-green-900'],
          'cancelled' => ['Canceladas','bg-red-400/80 text-red-900'],
        ];
      @endphp
      @foreach($badges as $key => [$label, $classes])
      <div class="rounded-2xl shadow-md p-5 bg-white border border-gray-100 hover:shadow-lg transition">
        <p class="text-sm font-medium text-gray-500 mb-1">{{ $label }}</p>
        <span class="px-3 py-1 text-sm font-bold rounded-full {{ $classes }}">{{ $visits[$key] }}</span>
      </div>
      @endforeach
    </section>

    <!-- GRÁFICA -->
    <section class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-10">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-700">
          @if(request('from') || request('to'))
            Visitas por día ({{ optional($from)->toDateString() }} — {{ optional($to)->toDateString() }})
          @else
            Visitas por día (últimos 30 días)
          @endif
        </h2>
      </div>
      <canvas id="visitsChart" height="120"></canvas>
    </section>

    <!-- TOP PROPIEDADES -->
    <section class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Top 5 Propiedades por Visitas</h2>
      @if($topProps->isEmpty())
        <p class="text-gray-500">No hay visitas en el rango seleccionado.</p>
      @else
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="py-3 px-4 font-medium">Propiedad</th>
                <th class="py-3 px-4 text-center font-medium">Visitas</th>
                <th class="py-3 px-4 text-right font-medium">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($topProps as $row)
              <tr class="hover:bg-gray-50">
                <td class="py-3 px-4">{{ $row->property?->title ?? 'Propiedad #'.$row->property_id }}</td>
                <td class="py-3 px-4 text-center text-blue-600 font-semibold">{{ $row->total }}</td>
                <td class="py-3 px-4 text-right">
                  <a href="{{ route('properties.edit', $row->property_id) }}" class="text-blue-600 hover:underline">Editar</a>
                  <span class="mx-2 text-gray-300">|</span>
                  <a href="{{ route('properties.show', $row->property_id) }}" class="text-indigo-600 hover:underline">Ver</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </section>
  </main>

  <!-- FOOTER -->
  <x-main-footer />


  <!-- Chart Script -->
  <script>
    const ctx = document.getElementById('visitsChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($labels),
          datasets: [{
            label: 'Visitas',
            data: @json($values),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.35
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1 },
              grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
              grid: { color: 'rgba(0,0,0,0.05)' }
            }
          }
        }
      });
    }
  </script>

</body>
</html>
