<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Visitas y Reservaciones</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 text-gray-800 flex flex-col">
  {{-- HEADER --}}
  <x-main-header />

  <main class="flex-1 max-w-7xl mx-auto px-6 py-12" x-data="{ activeTab: 'visits' }">
    <header class="mb-10">
      <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Gestión de visitas y reservaciones</h1>
      <p class="text-gray-500 text-lg">Consulta tus visitas agendadas y las reservaciones activas en un mismo lugar.</p>
    </header>

    <!-- SWITCH ENTRE PESTAÑAS -->
    <div class="flex justify-center mb-10">
      <div class="inline-flex rounded-xl shadow-sm overflow-hidden bg-white/70 backdrop-blur-sm border border-gray-100">
        <button 
          @click="activeTab = 'visits'" 
          :class="activeTab === 'visits' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50'" 
          class="px-6 py-2 text-sm font-semibold transition">
          Visitas
        </button>
        <button 
          @click="activeTab = 'reservations'" 
          :class="activeTab === 'reservations' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50'" 
          class="px-6 py-2 text-sm font-semibold transition">
          Reservaciones
        </button>
      </div>
    </div>

    <!-- CONTENIDO DE VISITAS -->
    <section x-show="activeTab === 'visits'" x-transition>
      <div class="bg-white/70 backdrop-blur-md border border-gray-100 rounded-2xl shadow-md p-8 mb-10">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold text-gray-800">Calendario de visitas</h2>
          <span class="text-sm text-gray-500">Solo propiedades en venta</span>
        </div>
        <div id="visits-calendar"></div>
      </div>
    </section>

    <!-- CONTENIDO DE RESERVACIONES -->
    <section x-show="activeTab === 'reservations'" x-transition>
      <div class="bg-white/70 backdrop-blur-md border border-gray-100 rounded-2xl shadow-md p-8 mb-10">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold text-gray-800">Calendario de reservaciones</h2>
          <span class="text-sm text-gray-500">Reservaciones activas y pasadas</span>
        </div>
        <div id="reservations-calendar"></div>
      </div>
    </section>
    <!-- LISTA DE VISITAS -->
    <section class="bg-white/70 backdrop-blur-md border border-gray-100 rounded-2xl shadow-md p-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Listado de visitas</h2>
        <a href="{{ route('agent.visits.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
          + Nueva visita
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full border-collapse text-sm">
          <thead>
            <tr class="bg-gray-100 text-gray-700">
              <th class="px-4 py-3 text-left font-semibold">Fecha / hora</th>
              <th class="px-4 py-3 text-left font-semibold">Propiedad</th>
              <th class="px-4 py-3 text-left font-semibold">Cliente</th>
              <th class="px-4 py-3 text-left font-semibold">Estado</th>
              <th class="px-4 py-3 text-right font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($visits as $visit)
              <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3">{{ $visit->visit_date ? $visit->visit_date->format('Y-m-d H:i') : '—' }}</td>
                <td class="px-4 py-3">{{ $visit->property?->title ?? '—' }}</td>
                <td class="px-4 py-3">{{ $visit->client?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                  <form action="{{ route('agent.visits.status', $visit) }}" method="POST">
                    @csrf @method('PATCH')
                      <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                      @php
                        $estados = [
                            'pending'   => 'Pendiente',
                            'confirmed' => 'Confirmada',
                            'completed' => 'Completada',
                            'cancelled' => 'Cancelada',
                        ];
                    @endphp

                    @foreach($estados as $key => $label)
                        <option value="{{ $key }}" @selected($visit->status === $key)>{{ $label }}</option>
                    @endforeach
                    </select>
                  </form>
                </td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('agent.visits.edit', $visit) }}" class="text-blue-600 hover:underline font-medium">Editar</a>
                  <span class="mx-2 text-gray-300">|</span>
                  <form action="{{ route('agent.visits.destroy', $visit) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta visita?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline font-medium">Eliminar</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-gray-500 py-6">No tienes visitas registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($visits, 'links'))
        <div class="mt-8">
          {{ $visits->links() }}
        </div>
      @endif
    </section>
  </main>

  {{-- FOOTER --}}
 <x-main-footer />

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const visits = new FullCalendar.Calendar(document.getElementById('visits-calendar'), {
      initialView: 'dayGridMonth',
      height: 'auto',
      locale: 'es',
      nowIndicator: true,
      eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      eventSources: [{
        url: '{{ route('agent.visits.feed') }}',
        method: 'GET',
        failure: () => alert('No se pudo cargar el calendario de visitas.'),
      }],
    });
    visits.render();

    const reservations = new FullCalendar.Calendar(document.getElementById('reservations-calendar'), {
      initialView: 'dayGridMonth',
      height: 'auto',
      locale: 'es',
      nowIndicator: true,
      eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      eventSources: [{
        url: '{{ route('agent.reservations.feed') }}',
        method: 'GET',
        failure: () => alert('No se pudo cargar el calendario de reservaciones.'),
      }],
    });
    reservations.render();
  });
  </script>
</body>
</html>
