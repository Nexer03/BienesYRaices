@php
    $authUser = $authUser ?? auth()->user();
    $isClient = $authUser && $conversation->client_id === $authUser->id;
    $isAgent  = $authUser && $conversation->agent_id === $authUser->id;
@endphp

@if($property)
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <h5 class="mb-1">{{ $property->title }}</h5>
      <p class="text-muted mb-2">{{ $property->listing_type === 'sale' ? 'Propiedad en venta' : 'Propiedad en renta' }}</p>
      <p class="fw-semibold fs-5 mb-3">
        ${{ number_format($property->price, $property->listing_type === 'rent' ? 2 : 0) }}
        <small class="text-muted fs-6">{{ $property->listing_type === 'rent' ? 'MXN / noche' : 'MXN' }}</small>
      </p>
      <a href="{{ route('properties.show', $property) }}" class="btn btn-outline-primary w-100">
        Ver detalles de la propiedad
      </a>
    </div>
  </div>
@endif

@if($property && $property->listing_type === 'sale')
  <div class="card shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">
      Agenda de visita
    </div>
    <div class="card-body">
      @if($nextVisit)
        <div class="mb-3">
          <p class="text-muted mb-1">Próxima cita</p>
          <p class="fw-semibold mb-1">
            {{ $nextVisit->visit_date->timezone(config('app.timezone'))->translatedFormat('d \d\e F, H:i') }}
          </p>
          <span class="badge bg-{{ $nextVisit->status === 'confirmed' ? 'success' : ($nextVisit->status === 'cancelled' ? 'danger' : 'warning text-dark') }} text-uppercase">{{ $nextVisit->status }}</span>
          @if($nextVisit->notes)
            <p class="small text-muted mt-2 mb-0">{{ $nextVisit->notes }}</p>
          @endif
        </div>

        <div class="d-flex flex-column gap-2">
          @if($isClient && $nextVisit->status === 'pending')
            <form method="POST" action="{{ route('chat.visits.confirm', [$conversation, $nextVisit]) }}">
              @csrf
              @method('PATCH')
              <button class="btn btn-success w-100" type="submit">Confirmar asistencia</button>
            </form>
          @endif
          <form method="POST" action="{{ route('chat.visits.cancel', [$conversation, $nextVisit]) }}">
            @csrf
            @method('PATCH')
            <button class="btn btn-outline-danger w-100" type="submit">Cancelar cita</button>
          </form>
        </div>
        <hr>
      @else
        <p class="text-muted">Aún no hay una visita programada.</p>
      @endif

      <form method="POST" action="{{ route('chat.visits.store', $conversation) }}" class="d-grid gap-2">
        @csrf
        <label class="form-label text-uppercase small mb-0">Fecha y hora</label>
        <input type="datetime-local"
               name="visit_date"
               class="form-control"
               min="{{ now()->format('Y-m-d\TH:i') }}"
               value="{{ old('visit_date', optional($nextVisit?->visit_date)->format('Y-m-d\TH:i') ?? now()->addDay()->format('Y-m-d\TH:i')) }}"
               required>
        @error('visit_date')
          <div class="text-danger small">{{ $message }}</div>
        @enderror

        <label class="form-label text-uppercase small mb-0">Notas</label>
        <textarea name="notes" class="form-control" rows="2" placeholder="Detalles opcionales">{{ old('notes') }}</textarea>
        @error('notes')
          <div class="text-danger small">{{ $message }}</div>
        @enderror

        <button class="btn btn-primary mt-2" type="submit">
          {{ $isAgent ? 'Agendar visita' : 'Solicitar visita' }}
        </button>
      </form>
    </div>
  </div>
@endif

@if($property && $property->listing_type === 'rent')
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">
      Reservación
    </div>
    <div class="card-body">
      @if($activeReservation)
        <p class="text-muted mb-1">Última reservación</p>
        <p class="fw-semibold mb-1">
          {{ optional($activeReservation->start_date)->format('d/m/Y') }} – {{ optional($activeReservation->end_date)->format('d/m/Y') }}
        </p>
        <p class="small mb-2">Estado: <strong>{{ ucfirst($activeReservation->status ?? 'pending') }}</strong> · Pago: <strong>{{ strtoupper($activeReservation->payment_status ?? 'unpaid') }}</strong></p>
        <p class="fw-bold fs-5">${{ number_format($activeReservation->total_price ?? 0, 2) }} MXN</p>
        <a href="{{ route('reservations.checkout', $activeReservation) }}" class="btn btn-primary w-100">Ir al checkout</a>
      @elseif($isClient)
        <form method="POST" action="{{ route('chat.reservations.store', $conversation) }}" class="d-grid gap-2">
          @csrf
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label text-uppercase small mb-0">Entrada</label>
              <input type="date" name="start_date" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('start_date', now()->addDay()->toDateString()) }}" required>
              @error('start_date')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-6">
              <label class="form-label text-uppercase small mb-0">Salida</label>
              <input type="date" name="end_date" class="form-control" min="{{ now()->addDay()->toDateString() }}" value="{{ old('end_date', now()->addDays(3)->toDateString()) }}" required>
              @error('end_date')
                <div class="text-danger small">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <button class="btn btn-primary mt-2" type="submit">Reservar y continuar al pago</button>
        </form>
      @else
        <p class="text-muted mb-0">El cliente aún no ha generado una reservación.</p>
      @endif
    </div>
  </div>
@endif
