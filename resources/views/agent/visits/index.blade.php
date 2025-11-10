@extends('layouts.app')

@section('title', 'Mis visitas')

@section('content')
<div class="container py-4">
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

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Mis visitas</h3>
    <a class="btn btn-primary" href="{{ route('agent.visits.create') }}">
      <i class="bi bi-plus-lg me-1"></i> Nueva visita
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card mb-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Calendario de visitas</h5>
      <small class="text-muted">Solo propiedades en venta</small>
    </div>
    <div id="agent-calendar"></div>
  </div>
</div>


  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="min-width:160px;">Fecha / hora</th>
              <th>Propiedad</th>
              <th>Cliente</th>
              <th style="width:210px;">Estado</th>
              <th class="text-end" style="width:180px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($visits as $visit)
              <tr>
                <td>
                  @if($visit->visit_date)
                    {{ $visit->visit_date->format('Y-m-d H:i') }}
                  @else
                    —
                  @endif
                </td>
                <td>{{ $visit->property?->title ?? '—' }}</td>
                <td>{{ $visit->client?->name ?? '—' }}</td>
                <td>
                  <form action="{{ route('agent.visits.status', $visit) }}" method="POST" class="d-inline">
                    @csrf @method('PATCH')
                    <div class="input-group input-group-sm" style="max-width:200px;">
                      <select name="status" class="form-select" onchange="this.form.submit()">
                        @foreach(['pending','confirmed','completed','cancelled'] as $st)
                          <option value="{{ $st }}" @selected($visit->status === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                      </select>
                      <button class="btn btn-outline-secondary" title="Guardar">
                        <i class="bi bi-check2"></i>
                      </button>
                    </div>
                  </form>
                </td>
                <td class="text-end">
                  <a href="{{ route('agent.visits.edit', $visit) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar
                  </a>
                  <form action="{{ route('agent.visits.destroy', $visit) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar esta visita?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i> Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No tienes visitas registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if(method_exists($visits, 'links'))
      <div class="card-footer">
        {{ $visits->links() }}
      </div>
    @endif
  </div>

</div>
{{-- FullCalendar --}}

<style>
  .fc-badge {
    display:inline-block; padding:2px 6px; border-radius:999px;
    font-size:11px; font-weight:600; line-height:1; margin-left:6px; white-space:nowrap;
  }
  .fc-badge-pending   { color:#664d03; background:#fff3cd; border:1px solid #ffecb5; }
  .fc-badge-confirmed { color:#0a58ca; background:#cfe2ff; border:1px solid #b6d4fe; }
  .fc-badge-completed { color:#0f5132; background:#d1e7dd; border:1px solid #badbcc; }
  .fc-badge-cancelled { color:#842029; background:#f8d7da; border:1px solid #f5c2c7; }

  /* permitir texto en varias líneas */
  .fc .fc-event-main { white-space: normal; }
  .fc .fc-daygrid-event,
  .fc .fc-timegrid-event { line-height:1.2; }
  .fc .fc-timegrid-event .fc-event-main { padding: 2px 6px; font-size: 12px; }
</style>




<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const el = document.getElementById('agent-calendar');
  if (!el) return;

  // --- helpers para badges ---
  function statusLabel(s) {
    switch (s) {
      case 'pending':   return 'Pendiente';
      case 'confirmed': return 'Confirmada';
      case 'completed': return 'Completada';
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
    contentHeight: 600,
    expandRows: false,
    nowIndicator: true,
    locale: 'es',

    dayMaxEventRows: 3,
    moreLinkClick: 'popover',
    eventDisplay: 'block',
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },

    // --- contenido del evento: hora + título + badge ---
    eventContent: function(arg) {
      const s = arg.event.extendedProps?.status;
      const badge = s
        ? `<span class="fc-badge ${statusClass(s)}">${statusLabel(s)}</span>`
        : '';
      const time  = arg.timeText ? `<div><strong>${arg.timeText}</strong></div>` : '';
      const title = `<div>${arg.event.title} ${badge}</div>`;
      return { html: `${time}${title}` };
    },

    // --- tooltip con info completa ---
    eventDidMount(info) {
      const s = info.event.extendedProps?.status ? ` — ${statusLabel(info.event.extendedProps.status)}` : '';
      const client = info.event.extendedProps?.client ? `\nCliente: ${info.event.extendedProps.client}` : '';
      info.el.setAttribute('title', `${info.event.title}${s}${client}`);
    },

    // --- rango visible, slots compactos y scroll cómodo en week/day ---
    slotMinTime: '08:00:00',
    slotMaxTime: '20:00:00',
    slotDuration: '00:30:00',
    slotLabelInterval: '01:00',
    allDaySlot: false,
    scrollTime: '08:00:00',
    scrollTimeReset: false,

    views: {
      dayGridMonth: {
        dayMaxEventRows: 4,
      },
      timeGridWeek: {
        dayHeaderFormat: { weekday: 'short', day: '2-digit', month: '2-digit' },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,
      },
      timeGridDay: {
        dayHeaderFormat: { weekday: 'long', day: '2-digit', month: 'long' },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,
      }
    },

    eventSources: [
      {
        url: '{{ route('agent.visits.feed') }}',
        method: 'GET',
        failure: function() { alert('No se pudo cargar el calendario de visitas.'); },
      }
    ],

    eventClick: function(info) {
      // por ahora navegamos al edit (default)
      // si quieres modal: info.jsEvent.preventDefault();
    },

    datesSet: function() {
      const scroller =
        el.querySelector('.fc-timegrid .fc-scroller, .fc-timegrid-body .fc-scroller');
      if (!scroller) return;
      scroller.addEventListener('wheel', function(e) {
        if (!el.querySelector('.fc-timegrid')) return;
        if (e.deltaY !== 0) {
          e.preventDefault();
          scroller.scrollTop += e.deltaY;
        }
      }, { passive: false });
    },
  });

  calendar.render();
});
</script>

@endsection
