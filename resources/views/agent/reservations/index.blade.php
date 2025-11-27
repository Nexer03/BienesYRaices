@extends('layouts.app')

@section('title', 'Calendario de Reservaciones')

@section('content')
<div class="container mx-auto px-4 py-6">
  {{-- Navegación entre secciones --}}
<div class="flex justify-center mb-6">
  <div class="inline-flex rounded-md shadow-sm" role="group">
    <a href="{{ route('agent.visits.index') }}"
      class="px-4 py-2 text-sm font-medium 
             {{ request()->routeIs('agent.visits.index') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} 
             rounded-l-lg focus:ring-2 focus:ring-blue-400">
      <i class="bi bi-calendar-check me-1"></i> Visitas
    </a>
    <a href="{{ route('agent.reservations.index') }}"
      class="px-4 py-2 text-sm font-medium 
             {{ request()->routeIs('agent.reservations.index') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} 
             rounded-r-lg focus:ring-2 focus:ring-blue-400">
      <i class="bi bi-house-door me-1"></i> Reservaciones
    </a>
  </div>
</div>

  <div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4 md:mb-0">
      Reservaciones de mis propiedades
    </h2>
    <span class="text-sm text-gray-500">Visualiza todas las reservaciones activas y pasadas</span>
  </div>

  {{-- Alertas --}}
  @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Tarjeta del calendario --}}
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-3">
      
    </div>
    <div id="reservations-calendar"></div>
  </div>
</div>

{{-- ======= FullCalendar Styles ======= --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

<style>
  .fc-badge {
    display:inline-block; padding:2px 6px; border-radius:999px;
    font-size:11px; font-weight:600; line-height:1; margin-left:6px; white-space:nowrap;
  }
  .fc-badge-pending   { color:#92400e; background:#fef3c7; border:1px solid #fde68a; }
  .fc-badge-confirmed { color:#1e3a8a; background:#dbeafe; border:1px solid #bfdbfe; }
  .fc-badge-completed { color:#065f46; background:#d1fae5; border:1px solid #a7f3d0; }
  .fc-badge-cancelled { color:#7f1d1d; background:#fee2e2; border:1px solid #fecaca; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const el = document.getElementById('reservations-calendar');
  if (!el) return;

  function statusLabel(s) {
    switch (s) {
      case 'pending':   return 'Pendiente';
      case 'confirmed': return 'Confirmada';
      case 'completed': return 'Finalizada';
      case 'cancelled': return 'Cancelada';
      default:          return s || '';
    }
  }
  function statusClass(s) {
    return {
      pending:   'fc-badge-pending',
      confirmed: 'fc-badge-confirmed',
      completed: 'fc-badge-completed',
      cancelled: 'fc-badge-cancelled',
    }[s] || 'fc-badge-pending';
  }

  const calendar = new FullCalendar.Calendar(el, {
    initialView: 'dayGridMonth',
    height: 'auto',
    locale: 'es',
    nowIndicator: true,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

    // --- contenido del evento ---
    eventContent(arg) {
      const s = arg.event.extendedProps?.status;
      const badge = s
        ? `<span class="fc-badge ${statusClass(s)}">${statusLabel(s)}</span>`
        : '';
      const time  = arg.timeText ? `<div><strong>${arg.timeText}</strong></div>` : '';
      const title = `<div>${arg.event.title} ${badge}</div>`;
      return { html: `${time}${title}` };
    },

    // --- tooltip de evento ---
    eventDidMount(info) {
      const s = info.event.extendedProps?.status ? ` — ${statusLabel(info.event.extendedProps.status)}` : '';
      const client = info.event.extendedProps?.client ? `\nCliente: ${info.event.extendedProps.client}` : '';
      info.el.setAttribute('title', `${info.event.title}${s}${client}`);
    },

    // --- cargar eventos desde backend ---
    eventSources: [
      {
        url: '{{ route('agent.reservations.feed') }}',
        method: 'GET',
        failure: function() {
          alert('No se pudo cargar el calendario de reservaciones.');
        },
      }
    ],
  });

  calendar.render();
});
</script>
@endsection
