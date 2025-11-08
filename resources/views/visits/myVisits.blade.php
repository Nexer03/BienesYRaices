<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Visitas y Reservas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- HEADER estándar -->
  <header class="sticky top-0 bg-white shadow-sm z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
      
      <!-- Logo -->
      <div class="flex items-center space-x-3">
        <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600 flex items-center">
          <i class="fas fa-home mr-2"></i>
          Sin beca <span class="text-gray-700"> no hay renta </span>
        </a>
      </div>

      <!-- Navegación -->
      <nav class="flex items-center space-x-4 text-sm font-medium text-gray-700">
        <a href="{{ route('properties.map') }}"
           class="px-5 py-2.5 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">
           Mapa
        </a>

        @auth
          <a href="{{ url('/dashboard') }}"
             class="px-5 py-2.5 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition">
             Perfil
          </a>
        @else
          <button type="button" onclick="openLoginModal()"
                  class="px-5 py-2.5 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition">
            Iniciar sesión
          </button>
        @endauth
      </nav>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="max-w-4xl mx-auto mt-12 px-6 mb-16 text-center" x-data="{ tab: '{{ $activeTab }}' }">

    <!-- Encabezado visual -->
    <div class="flex flex-col items-center mb-8">
      <div class="flex items-center justify-center bg-blue-100 text-blue-700 w-16 h-16 rounded-full mb-3 shadow-inner">
        <i class="fas fa-calendar-check text-2xl"></i>
      </div>
      <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
        Mis <span class="text-blue-600">Visitas</span> y <span class="text-blue-600">Reservas</span>
      </h1>
      <p class="text-gray-500 mt-2 text-sm">Consulta tus próximas visitas y reservas confirmadas.</p>
    </div>

    <!-- Tabs -->
    <div class="flex justify-center mb-8 space-x-3">
      <button @click="tab = 'visits'"
              :class="tab === 'visits' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
              class="px-5 py-2.5 rounded-full font-medium transition-all duration-300">
        Mis Visitas
      </button>

      <button @click="tab = 'reservations'"
              :class="tab === 'reservations' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
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
             {{ request('status') === $key ? 'bg-blue-500 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
             {{ $label }}
          </a>
        @endforeach
      </div>

      <!-- Listado de visitas -->
      @if($visits->count() > 0)
        <div class="space-y-4">
          @foreach($visits as $visit)
          <div class="bg-white p-6 rounded-lg shadow-md border-l-4 transition
              @if($visit->status == 'pending') border-yellow-500
              @elseif($visit->status == 'confirmed') border-green-500
              @elseif($visit->status == 'completed') border-blue-500
              @elseif($visit->status == 'cancelled') border-red-500
              @endif" x-transition.duration.300ms>
            <div class="flex justify-between items-start mb-2">
              <h3 class="text-xl font-semibold">{{ $visit->property->title }}</h3>
              <span class="px-3 py-1 rounded-full text-sm font-medium
                  @if($visit->status == 'pending') bg-yellow-100 text-yellow-800
                  @elseif($visit->status == 'confirmed') bg-green-100 text-green-800
                  @elseif($visit->status == 'completed') bg-blue-100 text-blue-800
                  @elseif($visit->status == 'cancelled') bg-red-100 text-red-800
                  @endif">
                  {{ ucfirst($visit->status) }}
              </span>
            </div>

            <p class="text-gray-600 mb-2">{{ $visit->property->location }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
              <div><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d/m/Y H:i') }}</div>
              <div><strong>Agente:</strong> {{ $visit->agent->name }}</div>
              <div><strong>Precio:</strong> ${{ number_format($visit->property->price, 2) }}</div>
              <div><strong>Tipo:</strong> {{ $visit->property->listing_type == 'rent' ? 'Renta' : 'Venta' }}</div>
            </div>

            @if($visit->notes)
              <div class="mt-3 p-3 bg-gray-50 rounded">
                <strong>Notas:</strong> {{ $visit->notes }}
              </div>
            @endif
          </div>
          @endforeach
        </div>
      @else
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
          <p class="text-gray-500 text-lg">No tienes visitas agendadas.</p>
          <a href="{{ route('properties.map') }}" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Explorar propiedades</a>
        </div>
      @endif
    </div>

    <!-- RESERVAS -->
    <div x-show="tab === 'reservations'" x-transition.duration.300ms x-cloak>
      @if($reservations->count() > 0)
        <div class="space-y-4">
          @foreach($reservations as $res)
          <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500 transition">
            <h3 class="text-xl font-semibold text-blue-700">{{ $res->property->title }}</h3>
            <p class="text-gray-600 mb-2">{{ $res->property->location }}</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700">
              <div><strong>Entrada:</strong> {{ \Carbon\Carbon::parse($res->start_date)->format('d/m/Y') }}</div>
              <div><strong>Salida:</strong> {{ \Carbon\Carbon::parse($res->end_date)->format('d/m/Y') }}</div>
              <div><strong>Noches:</strong> {{ $res->nights }}</div>
              <div><strong>Total:</strong> ${{ number_format($res->total_price, 2) }} MXN</div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <div class="bg-white p-8 rounded-lg shadow-md text-center">
          <p class="text-gray-500 text-lg">No tienes reservas.</p>
          <a href="{{ route('properties.map') }}" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Explorar propiedades</a>
        </div>
      @endif
    </div>
  </main>

  <!-- FOOTER estándar -->
  <footer class="bg-gray-800 text-white py-8 mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
          <div class="flex items-center text-white font-bold text-xl mb-4">
            <i class="fas fa-home mr-2"></i>
            <span>SIN BECA NO HAY RENTA</span>
          </div>
          <p class="text-gray-300 mb-4">
            Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades con sus futuros dueños de manera eficiente y profesional.
          </p>
        </div>

        <div>
          <h3 class="font-semibold text-lg mb-4">Navegación</h3>
          <ul class="space-y-2 text-gray-300">
            <li><a href="{{ route('properties.map') }}" class="hover:text-white transition">Mapa</a></li>
            <li><a href="{{ route('visits.my') }}" class="hover:text-white transition">Mi Agenda</a></li>
            <li><a href="{{ route('agent.view') }}" class="hover:text-white transition">Modo Vendedor</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-lg mb-4">Contacto</h3>
          <ul class="space-y-2 text-gray-300">
            <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> soporte@sinbeca.com</li>
            <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
            <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Ciudad, País</li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-300 text-sm">&copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <a href="#" class="text-gray-300 hover:text-white text-sm transition">Privacidad</a>
          <a href="#" class="text-gray-300 hover:text-white text-sm transition">Términos</a>
        </div>
      </div>
    </div>
  </footer>

</body>
</html>
