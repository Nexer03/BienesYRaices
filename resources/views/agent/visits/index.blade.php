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

  <style>
    .glass-card {
      @apply bg-white/70 backdrop-blur-md border border-gray-200 rounded-2xl shadow-xl;
    }
    .section-title {
      @apply text-xl font-bold text-gray-800 flex items-center;
    }
    .badge-gray {
      @apply text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-lg;
    }
    .th {
      @apply px-4 py-3 text-left font-semibold;
    }
    .td {
      @apply px-4 py-3 text-gray-700;
    }
  </style>

  <!-- STORE DE ALPINE PARA CONTROLAR LOS TABS -->
  <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('activeTab', 'visits');
    });
  </script>
</head>

<body class="min-h-screen bg-gradient-to-b from-gray-50 via-gray-100 to-gray-200 text-gray-800 flex flex-col">

  {{-- HEADER --}}
  <x-main-header />

  <main class="flex-1 max-w-7xl mx-auto px-6 py-12" x-data>

    <!-- HEADER -->
    <header class="mb-12">
      <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight mb-2">
        Gestión de visitas y reservaciones
      </h1>
      <p class="text-gray-600 text-lg">
        Consulta tus visitas agendadas y reservaciones desde un solo panel.
      </p>
    </header>

    <!-- SWITCH ENTRE TABS -->
    <div class="flex justify-center mb-12">
      <div class="bg-white/80 backdrop-blur-lg border border-gray-200 shadow-lg rounded-2xl flex p-1">

        <button 
          @click="$store.activeTab = 'visits'"
          :class="$store.activeTab === 'visits' 
            ? 'bg-blue-600 text-white shadow px-6' 
            : 'text-gray-600 hover:bg-gray-100 px-6'"
          class="py-2 rounded-xl text-sm font-semibold transition-all">
          <i class="fa-solid fa-calendar-check mr-2"></i> Visitas
        </button>

        <button 
          @click="$store.activeTab = 'reservations'"
          :class="$store.activeTab === 'reservations' 
            ? 'bg-blue-600 text-white shadow px-6' 
            : 'text-gray-600 hover:bg-gray-100 px-6'"
          class="py-2 rounded-xl text-sm font-semibold transition-all">
          <i class="fa-solid fa-calendar-days mr-2"></i> Reservaciones
        </button>

      </div>
    </div>

    <!-- CALENDARIO VISITAS -->
    <section x-show="$store.activeTab === 'visits'" x-transition>
      <div class="glass-card p-8 mb-10">
        <div class="flex items-center justify-between mb-5">
          <h2 class="section-title"><i class="fa-solid fa-house mr-2"></i> Calendario de visitas</h2>
          <span class="badge-gray">Solo propiedades en venta</span>
        </div>
        <div id="visits-calendar"></div>
      </div>
    </section>

    <!-- CALENDARIO RESERVACIONES -->
    <section x-show="$store.activeTab === 'reservations'" x-transition>
      <div class="glass-card p-8 mb-10">
        <div class="flex items-center justify-between mb-5">
          <h2 class="section-title"><i class="fa-solid fa-bookmark mr-2"></i> Calendario de reservaciones</h2>
          <span class="badge-gray">Activas y pasadas</span>
        </div>
        <div id="reservations-calendar"></div>
      </div>
    </section>

    <!-- LISTA DE VISITAS -->
    <section class="glass-card p-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="section-title">
          <i class="fa-solid fa-list mr-2"></i> Listado de visitas
        </h2>

        <a href="{{ route('agent.visits.create') }}"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow font-semibold transition-all">
          <i class="fa-solid fa-plus mr-1"></i> Nueva visita
        </a>
      </div>

      <div class="overflow-x-auto rounded-xl border border-gray-100">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-gray-100/60 text-gray-700">
              <th class="th">Fecha / hora</th>
              <th class="th">Propiedad</th>
              <th class="th">Cliente</th>
              <th class="th">Estado</th>
              <th class="th text-right">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            @forelse($visits as $visit)
            <tr class="hover:bg-gray-50 transition">
              <td class="td">{{ $visit->visit_date?->format('Y-m-d H:i') ?? '—' }}</td>
              <td class="td">{{ $visit->property?->title ?? '—' }}</td>
              <td class="td">{{ $visit->client?->name ?? '—' }}</td>

              <td class="td">
                <form action="{{ route('agent.visits.status', $visit) }}" method="POST">
                  @csrf @method('PATCH')

                  <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach(['pending'=>'Pendiente','confirmed'=>'Confirmada','completed'=>'Completada','cancelled'=>'Cancelada'] as $key => $label)
                    <option value="{{ $key }}" @selected($visit->status === $key)>{{ $label }}</option>
                    @endforeach
                  </select>
                </form>
              </td>

              <td class="td text-right">
                <a href="{{ route('agent.visits.edit', $visit) }}" class="text-blue-600 hover:underline font-medium">Editar</a>
                <span class="mx-2 text-gray-300">|</span>
                <form action="{{ route('agent.visits.destroy', $visit) }}" method="POST" class="inline"
                  onsubmit="return confirm('¿Eliminar esta visita?');">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline font-medium" type="submit">Eliminar</button>
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

  <!-- SCRIPT FULLCALENDAR + FIX DE BUG -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {

      // CALENDARIO VISITAS
      window.visitsCalendar = new FullCalendar.Calendar(document.getElementById('visits-calendar'), {
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
          method: 'GET'
        }],
      });
      window.visitsCalendar.render();

      // CALENDARIO RESERVACIONES
      window.reservationsCalendar = new FullCalendar.Calendar(document.getElementById('reservations-calendar'), {
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
          method: 'GET'
        }],
      });
      window.reservationsCalendar.render();

    });

    // FIX CRÍTICO: FORZAR UPDATE AL CAMBIAR DE TAB
    document.addEventListener('alpine:init', () => {
      Alpine.effect(() => {
        const tab = Alpine.store('activeTab');

        setTimeout(() => {
          if (tab === 'visits' && window.visitsCalendar) {
            window.visitsCalendar.updateSize();
          }
          if (tab === 'reservations' && window.reservationsCalendar) {
            window.reservationsCalendar.updateSize();
          }
        }, 80);
      });
    });
  </script>

</body>
</html>
