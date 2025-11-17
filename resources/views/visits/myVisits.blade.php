<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Visitas y Reservas</title>

  {{-- Anti-flash: aplica tema guardado antes de cargar Tailwind --}}
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

  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">

   {{-- HEADER --}}
    <x-main-header />

  <!-- Botón flotante de tema -->
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="max-w-4xl mx-auto mt-12 px-6 mb-16 text-center flex-1" x-data="{ tab: '{{ $activeTab }}' }">

    <!-- Encabezado visual -->
    <div class="flex flex-col items-center mb-8">
      <div class="flex items-center justify-center bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 w-16 h-16 rounded-full mb-3 shadow-inner">
        <i class="fas fa-calendar-check text-2xl"></i>
      </div>
      <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100 tracking-tight">
        Mis <span class="text-blue-600">Visitas</span> y <span class="text-blue-600">Reservas</span>
      </h1>
      <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Consulta tus próximas visitas y reservas confirmadas.</p>
    </div>

    <!-- Tabs -->
    <div class="flex justify-center mb-8 space-x-3">
      <button @click="tab = 'visits'"
              :class="tab === 'visits'
                ? 'bg-blue-500 text-white shadow-md'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'"
              class="px-5 py-2.5 rounded-full font-medium transition-all duration-300">
        Mis Visitas
      </button>

      <button @click="tab = 'reservations'"
              :class="tab === 'reservations'
                ? 'bg-blue-500 text-white shadow-md'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'"
              class="px-5 py-2.5 rounded-full font-medium transition-all duration-300">
        Mis Reservas
      </button>
    </div>

    <!-- VISITAS -->
    <div x-show="tab === 'visits'" x-transition.duration.300ms x-cloak>
      <!-- Filtros -->
      <div class="mb-6 flex flex-wrap gap-2 justify-center">
        @php
          $statuses = [
            '' => 'Todas',
            'pending' => 'Pendientes',
            'confirmed' => 'Confirmadas',
            'completed' => 'Completadas',
            'cancelled' => 'Canceladas'
          ];
        @endphp
        @foreach($statuses as $key => $label)
          <a href="{{ request()->fullUrlWithQuery(['status' => $key]) }}"
             class="px-4 py-2 rounded-full transition
             {{ request('status') === $key
                  ? 'bg-blue-500 text-white shadow'
                  : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' }}">
             {{ $label }}
          </a>
        @endforeach
      </div>

      <!-- Listado de visitas -->
      @if($visits->count() > 0)
        <div class="space-y-4 text-left">
          @foreach($visits as $visit)
          <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 transition border-gray-200 dark:border-gray-800
              @if($visit->status == 'pending') border-yellow-500
              @elseif($visit->status == 'confirmed') border-green-500
              @elseif($visit->status == 'completed') border-blue-500
              @elseif($visit->status == 'cancelled') border-red-500
              @endif" x-transition.duration.300ms>
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $visit->property->title }}</h3>
              <span class="px-3 py-1 rounded-full text-sm font-medium
                  @if($visit->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200
                  @elseif($visit->status == 'confirmed') bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200
                  @elseif($visit->status == 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200
                  @elseif($visit->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200
                  @endif">
                  {{ ucfirst($visit->status) }}
              </span>
            </div>

            <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $visit->property->location }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
              <div><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d/m/Y H:i') }}</div>
              <div><strong>Agente:</strong> {{ $visit->agent->name }}</div>
              <div><strong>Precio:</strong> ${{ number_format($visit->property->price, 2) }}</div>
              <div><strong>Tipo:</strong> {{ $visit->property->listing_type == 'rent' ? 'Renta' : 'Venta' }}</div>
            </div>

            @if($visit->notes)
              <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                <strong>Notas:</strong> <span class="text-gray-700 dark:text-gray-200">{{ $visit->notes }}</span>
              </div>
            @endif
          </div>
          @endforeach
        </div>
      @else
        <div class="bg-white dark:bg-gray-900 p-8 rounded-lg shadow-md text-center">
          <p class="text-gray-500 dark:text-gray-400 text-lg">No tienes visitas agendadas.</p>
          <a href="{{ route('properties.map') }}" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Explorar propiedades</a>
        </div>
      @endif
    </div>

    <!-- RESERVAS -->
    <div x-show="tab === 'reservations'" x-transition.duration.300ms x-cloak>
      @if($reservations->count() > 0)
        <div class="space-y-4 text-left">
          @foreach($reservations as $res)
          <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 border-blue-500 transition">
            <h3 class="text-xl font-semibold text-blue-700 dark:text-blue-300">{{ $res->property->title }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $res->property->location }}</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700 dark:text-gray-300">
              <div><strong>Entrada:</strong> {{ \Carbon\Carbon::parse($res->start_date)->format('d/m/Y') }}</div>
              <div><strong>Salida:</strong> {{ \Carbon\Carbon::parse($res->end_date)->format('d/m/Y') }}</div>
              <div><strong>Noches:</strong> {{ $res->nights }}</div>
              <div><strong>Total:</strong> ${{ number_format($res->total_price, 2) }} MXN</div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <div class="bg-white dark:bg-gray-900 p-8 rounded-lg shadow-md text-center">
          <p class="text-gray-500 dark:text-gray-400 text-lg">No tienes reservas.</p>
          <a href="{{ route('properties.map') }}" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Explorar propiedades</a>
        </div>
      @endif
    </div>
  </main>

    {{-- Footer --}}
    <x-main-footer />

  {{-- Toggle de tema sincronizado con localStorage --}}
  <script>
    (function () {
      const html  = document.documentElement;
      const btn   = document.getElementById('theme-toggle');
      const icon  = btn?.querySelector('i');
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
        html.classList.toggle('dark', isDark);
        try { localStorage.setItem('theme', mode); } catch (e) {}
        setIconAndLabel();
      }

      // Inicializar según clase actual
      setIconAndLabel();

      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>

</body>
</html>
