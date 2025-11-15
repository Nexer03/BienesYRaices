@php
    $authUser = $authUser ?? auth()->user();
    $isClient = $authUser && $conversation->client_id === $authUser->id;
    $isAgent  = $authUser && $conversation->agent_id === $authUser->id;

    // El agente sólo puede crear cita si no hay una pendiente/confirmada
    $canCreateVisit = $isAgent && (
        !$nextVisit ||
        in_array($nextVisit->status, ['cancelled', 'completed'])
    );
@endphp

@if($property)
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h5 class="mb-1">{{ $property->title }}</h5>
      <p class="text-muted mb-2">
        {{ $property->listing_type === 'sale' ? 'Propiedad en venta' : 'Propiedad en renta' }}
      </p>
      <p class="fw-semibold fs-5 mb-3">
        ${{ number_format($property->price, $property->listing_type === 'rent' ? 2 : 0) }}
        <small class="text-muted fs-6">
          {{ $property->listing_type === 'rent' ? 'MXN / noche' : 'MXN' }}
        </small>
      </p>
      <a href="{{ route('properties.show', $property) }}" class="btn btn-outline-primary w-100">
        Ver detalles de la propiedad
      </a>
    </div>
  </div>
@endif

{{-- ==================== AGENDA DE VISITA (VENTA) ==================== --}}
@if($property && $property->listing_type === 'sale')
  <div class="card shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">
      Agenda de visita
    </div>
    <div class="card-body">

      @if($nextVisit)
        {{-- Hay una cita existente --}}
        <div class="mb-3 p-3 rounded-3 bg-light">
          <p class="text-muted mb-1">Próxima cita</p>
          <p class="fw-semibold mb-1">
            {{ $nextVisit->visit_date->timezone(config('app.timezone'))->translatedFormat('d \d\e F, H:i') }}
          </p>
          <span
            class="badge bg-{{ $nextVisit->status === 'confirmed'
                          ? 'success'
                          : ($nextVisit->status === 'cancelled' ? 'danger' : 'warning text-dark') }} text-uppercase">
            {{ strtoupper($nextVisit->status) }}
          </span>

          @if($nextVisit->notes)
            <p class="small text-muted mt-2 mb-0">{{ $nextVisit->notes }}</p>
          @endif
        </div>

        {{-- ACCIONES DEL CLIENTE --}}
        @if($isClient)
          <div class="d-flex flex-column gap-2 mb-3">
            @if($nextVisit->status === 'pending')
              <form method="POST" action="{{ route('chat.visits.confirm', [$conversation, $nextVisit]) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-success w-100" type="submit">
                  Confirmar asistencia
                </button>
              </form>
            @endif

            {{-- El cliente siempre puede cancelar mientras exista la cita --}}
            <form method="POST" action="{{ route('chat.visits.cancel', [$conversation, $nextVisit]) }}">
              @csrf
              @method('PATCH')
              <button class="btn btn-outline-danger w-100" type="submit">
                Cancelar cita
              </button>
            </form>
          </div>
        @endif

        {{-- Nota informativa cuando la ve el cliente --}}
        @if($isClient && $nextVisit->status === 'pending')
          <p class="small text-muted mb-0">
            El agente te propuso esta fecha. Puedes confirmarla o cancelarla desde aquí.
          </p>
        @endif

        {{-- Si hay cita pendiente/confirmada, el agente sólo ve el resumen y el botón de cancelar --}}
        @if($isAgent && in_array($nextVisit->status, ['pending','confirmed']))
          <p class="small text-muted mb-2">
            Ya hay una visita programada. Si necesitas cambiarla, primero cancela esta cita
            y luego podrás proponer una nueva fecha.
          </p>
          <form method="POST" action="{{ route('chat.visits.cancel', [$conversation, $nextVisit]) }}">
            @csrf
            @method('PATCH')
            <button class="btn btn-outline-danger w-100" type="submit">
              Cancelar cita
            </button>
          </form>
        @endif

      @else
        {{-- No hay cita programada --}}
        <div class="mb-3 p-3 rounded-3 bg-light">
          <p class="text-muted mb-1">Aún no hay una visita programada.</p>
          @if($isClient)
            <p class="small text-muted mb-0">
              El agente puede proponerte una fecha y hora para visitar la propiedad.
              Cuando te envíe la solicitud, aquí podrás confirmarla o cancelar la cita.
            </p>
          @else
            <p class="small text-muted mb-0">
              Puedes proponer una fecha y hora para visitar la propiedad. El cliente
              podrá aceptarla o rechazarla desde este mismo panel.
            </p>
          @endif
        </div>
      @endif

      {{-- FORMULARIO PARA CREAR / REPROGRAMAR SOLO PARA EL AGENTE --}}
      @if($canCreateVisit)
        <form method="POST" action="{{ route('chat.visits.store', $conversation) }}" class="d-grid gap-2">
          @csrf
          <label class="form-label text-uppercase small mb-0">Fecha y hora</label>
          <input
            type="datetime-local"
            name="visit_date"
            class="form-control"
            min="{{ now()->format('Y-m-d\TH:i') }}"
            value="{{ old('visit_date', now()->addDay()->format('Y-m-d\TH:i')) }}"
            required
          >
          @error('visit_date')
            <div class="text-danger small">{{ $message }}</div>
          @enderror

          <label class="form-label text-uppercase small mb-0">Notas</label>
          <textarea
            name="notes"
            class="form-control"
            rows="2"
            placeholder="Detalles opcionales (punto de reunión, referencias, etc.)"
          >{{ old('notes') }}</textarea>
          @error('notes')
            <div class="text-danger small">{{ $message }}</div>
          @enderror

          <button class="btn btn-primary mt-2" type="submit">
            Agendar visita
          </button>
        </form>
      @endif
    </div>
  </div>
@endif

{{-- ==================== RESERVAS (RENTAS) ==================== --}}
@if($property && $property->listing_type === 'rent')
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">
      Reservación
    </div>
    <div class="card-body">
      @if($activeReservation)
        <p class="text-muted mb-1">Última reservación</p>
        <p class="fw-semibold mb-1">
          {{ optional($activeReservation->start_date)->format('d/m/Y') }}
          –
          {{ optional($activeReservation->end_date)->format('d/m/Y') }}
        </p>
        <p class="small mb-2">
          Estado: <strong>{{ ucfirst($activeReservation->status ?? 'pending') }}</strong>
          · Pago: <strong>{{ strtoupper($activeReservation->payment_status ?? 'unpaid') }}</strong>
        </p>
        <p class="fw-bold fs-5">
          ${{ number_format($activeReservation->total_price ?? 0, 2) }} MXN
        </p>
        <a href="{{ route('reservations.checkout', $activeReservation) }}" class="btn btn-primary w-100">
          Ir al checkout
        </a>
      @elseif($isClient)
        <form method="POST" action="{{ route('chat.reservations.store', $conversation) }}" class="d-grid gap-2">
          @csrf
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label text-uppercase small mb-0">Entrada</label>
              <input
                type="date"
                name="start_date"
                class="form-control"
                min="{{ now()->toDateString() }}"
                value="{{ old('start_date', now()->addDay()->toDateString()) }}"
                required
              >
              @error('start_date')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-6">
              <label class="form-label text-uppercase small mb-0">Salida</label>
              <input
                type="date"
                name="end_date"
                class="form-control"
                min="{{ now()->addDay()->toDateString() }}"
                value="{{ old('end_date', now()->addDays(3)->toDateString()) }}"
                required
              >
              @error('end_date')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <button class="btn btn-primary mt-2" type="submit">
            Reservar y continuar al pago
          </button>
        </form>
      @else
        <p class="text-muted mb-0">
          El cliente aún no ha generado una reservación.
        </p>
      @endif
    </div>
  </div>
@endif
