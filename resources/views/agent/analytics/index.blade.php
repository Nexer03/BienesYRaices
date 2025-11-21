<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <title>Panel de estadísticas</title>

  <!-- Anti-flash: claro por defecto; aplica dark si fue guardado -->
  <script>
    (function () {
      try {
        let saved = localStorage.getItem('theme');
        if (!saved) { localStorage.setItem('theme', 'light'); saved = 'light'; }
        if (saved === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
      } catch(e){ document.documentElement.classList.remove('dark'); }
    })();
  </script>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script> tailwind.config = { darkMode: 'class' };</script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Fondo claro + overlay oscuro con fade */
    :root{
      --bg1-light:#f9fafb; /* gray-50 */
      --bg2-light:#e5e7eb; /* gray-200 */
      --bg1-dark:#0b1220;
      --bg2-dark:#0a0f1a;
    }
    body{
      background-image: linear-gradient(180deg, var(--bg1-light) 0%, var(--bg2-light) 100%);
      transition: color .45s ease, border-color .45s ease, box-shadow .45s ease;
      min-height: 100vh; position: relative;
    }
    body::before{
      content:""; position: fixed; inset:0; z-index:-1; pointer-events:none;
      background-image: linear-gradient(180deg, var(--bg1-dark) 0%, var(--bg2-dark) 100%);
      opacity: 0; transition: opacity .65s ease;
    }
    html.dark body::before{ opacity: 1; }

    /* Suaviza cambio de colores de los componentes */
    html.theme-fade *{
      transition: background-color .45s ease, color .45s ease, border-color .45s ease, fill .45s ease;
    }

    /* Botón tema */
    #theme-toggle{ transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, color .25s ease; }
    #theme-toggle.bounce{ transform: translateY(-1px) scale(1.03); box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    #theme-toggle-icon{ transition: transform .35s ease, opacity .2s ease; }
    #theme-toggle-icon.spin{ transform: rotate(180deg); }

    @media (prefers-reduced-motion: reduce){
      body, body::before, html.theme-fade * { transition: none !important; }
    }
  </style>
</head>

<body class="min-h-screen flex flex-col text-gray-800 dark:text-gray-100">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <!-- BOTÓN TEMA -->
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <!-- CONTENIDO -->
  <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-12">
    <header class="mb-10">
      <h1 class="text-4xl font-extrabold text-gray-800 dark:text-gray-100 mb-2">Estadísticas del Agente</h1>
      <p class="text-gray-500 dark:text-gray-300 text-lg">Monitorea tus propiedades, visitas y rendimiento general en un solo lugar.</p>
    </header>

    <!-- FILTROS -->
    <section class="bg-white/60 dark:bg-gray-900/70 backdrop-blur-md shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 mb-10">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-4">Filtrar por fecha</h2>
      <form method="GET" class="grid md:grid-cols-4 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Desde</label>
          <input type="date" name="from" value="{{ optional($from)->toDateString() }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
          <input type="date" name="to" value="{{ optional($to)->toDateString() }}"
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex items-end">
          <button type="submit" class="w-full bg-blue-600 text-white font-semibold rounded-lg py-2 hover:bg-blue-700 transition">Aplicar</button>
        </div>
        <div class="flex items-end">
          <a href="{{ route('agent.analytics') }}" class="w-full text-center border border-gray-300 dark:border-gray-700 rounded-lg py-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Limpiar</a>
        </div>
      </form>
    </section>

    <!-- RESUMEN DE MÉTRICAS -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      {{-- Propiedades Totales --}}
      <div class="bg-gradient-to-tr from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">Propiedades Totales</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $totalProps }}</h3>
      </div>

      {{-- En Venta --}}
      <div class="bg-gradient-to-tr from-blue-500 to-sky-500 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">En Venta</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $saleProps }}</h3>
      </div>

      {{-- En Renta --}}
      <div class="bg-gradient-to-tr from-indigo-500 to-blue-500 text-white rounded-2xl shadow-lg p-6">
        <p class="text-sm opacity-90">En Renta</p>
        <h3 class="text-4xl font-extrabold mt-2">{{ $rentProps }}</h3>
      </div>

      {{-- Visitas (90 días) --}}
      <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-2xl shadow-lg p-6">
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
      <div class="rounded-2xl shadow-md p-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:shadow-lg transition">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-1">{{ $label }}</p>
        <span class="px-3 py-1 text-sm font-bold rounded-full {{ $classes }}">{{ $visits[$key] }}</span>
      </div>
      @endforeach
    </section>

    <!-- GRÁFICA -->
    <section class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-8 mb-10">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-100">
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
    <section class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-8">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-4">Top 5 Propiedades por Visitas</h2>
      @if($topProps->isEmpty())
        <p class="text-gray-500 dark:text-gray-300">No hay visitas en el rango seleccionado.</p>
      @else
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
              <tr>
                <th class="py-3 px-4 font-medium">Propiedad</th>
                <th class="py-3 px-4 text-center font-medium">Visitas</th>
                <th class="py-3 px-4 text-right font-medium">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              @foreach($topProps as $row)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                <td class="py-3 px-4 text-gray-800 dark:text-gray-100">{{ $row->property?->title ?? 'Propiedad #'.$row->property_id }}</td>
                <td class="py-3 px-4 text-center text-blue-600 dark:text-blue-400 font-semibold">{{ $row->total }}</td>
                <td class="py-3 px-4 text-right">
                  <a href="{{ route('properties.edit', $row->property_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                  <span class="mx-2 text-gray-300">|</span>
                  <a href="{{ route('properties.show', $row->property_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Ver</a>
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
    // Render Chart (colores compatibles con ambos temas)
    const ctx = document.getElementById('visitsChart');
    let visitsChart = null;
    function buildChart() {
      if (!ctx) return;
      const isDark = document.documentElement.classList.contains('dark');
      const grid = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';
      const line = '#2563eb';
      const fill = isDark ? 'rgba(37,99,235,0.15)' : 'rgba(37,99,235,0.10)';
      if (visitsChart) { visitsChart.destroy(); }
      visitsChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: @json($labels),
          datasets: [{
            label: 'Visitas',
            data: @json($values),
            borderColor: line,
            backgroundColor: fill,
            fill: true,
            tension: 0.35
          }]
        },
        options: {
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1, color: isDark ? '#e5e7eb' : '#374151' },
              grid: { color: grid }
            },
            x: {
              ticks: { color: isDark ? '#e5e7eb' : '#374151' },
              grid: { color: grid }
            }
          }
        }
      });
    }
    buildChart();
  </script>

  <!-- Tema: toggle + fade + persistencia -->
  <script>
    (function () {
      const html = document.documentElement;
      const btn  = document.getElementById('theme-toggle');
      const icon = document.getElementById('theme-toggle-icon');
      const label= btn?.querySelector('span');

      function setIconAndLabel() {
        const isDark = html.classList.contains('dark');
        if (!icon || !label) return;
        icon.classList.remove('fa-sun','fa-moon');
        icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
        label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
      }

      function startPageFade() {
        html.classList.add('theme-fade');
        setTimeout(() => html.classList.remove('theme-fade'), 420);
      }

      function animateButton() {
        if (!btn || !icon) return;
        btn.classList.add('bounce'); icon.classList.add('spin');
        setTimeout(() => { btn.classList.remove('bounce'); icon.classList.remove('spin'); }, 350);
      }

      function apply(mode) {
        const isDark = mode === 'dark';
        startPageFade();
        html.classList.toggle('dark', isDark);
        try { localStorage.setItem('theme', mode); } catch(e){}
        setIconAndLabel();
        animateButton();
        // reconstruir chart para grid/labels
        if (typeof buildChart === 'function') buildChart();
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
