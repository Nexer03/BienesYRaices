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

{{-- Ajustes de estilo para los modales del chat (visita + reserva) --}}
<style>
  /* Contenedor de los modales en claro */
  #chatVisitModal > div,
  #chat-reservation-modal > div {
    background-color: #ffffff;
  }

  /* Overlay un poco más oscuro en dark */
  .dark #chatVisitModal,
  .dark #chat-reservation-modal {
    background: rgba(15, 23, 42, 0.85);
  }

  /* Caja interna de los modales en dark */
  .dark #chatVisitModal > div,
  .dark #chat-reservation-modal > div {
    background-color: #020617 !important; /* slate-950 */
    color: #e5e7eb;
    border: 1px solid #1f2937;            /* slate-800 */
  }

  .dark #chat-reservation-modal .text-muted {
    color: #9ca3af !important;
  }

  .dark #chat-reservation-modal input.form-control {
    background-color: #020617;
    color: #e5e7eb;
    border-color: #1f2937;
  }

  .dark #chat-priceSummary {
    background-color: #020617;
    border-color: #1f2937;
    color: #e5e7eb;
  }
</style>

{{-- ==================== TARJETA DE PROPIEDAD ==================== --}}
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

  @php
      // Horarios ocupados de visitas por día (para deshabilitar slots)
      $visitSlots = [];

      if (method_exists($property, 'visits')) {
          $futureVisits = $property->visits()
              ->whereDate('visit_date', '>=', now()->toDateString())
              ->where('status', '!=', 'cancelled')
              ->get();

          foreach ($futureVisits as $visit) {
              if (!$visit->visit_date) {
                  continue;
              }

              $date = \Illuminate\Support\Carbon::parse($visit->visit_date)->format('Y-m-d');
              $hour = \Illuminate\Support\Carbon::parse($visit->visit_date)->format('H:i'); // "08:00"

              $visitSlots[$date][] = $hour;
          }
      }
  @endphp

  <div class="card shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">
      Agenda de visita
    </div>
    <div class="card-body">

      {{-- RESUMEN DE CITA ACTUAL ------------------------------------ --}}
      @if($nextVisit)
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

            <form method="POST" action="{{ route('chat.visits.cancel', [$conversation, $nextVisit]) }}">
              @csrf
              @method('PATCH')
              <button class="btn btn-outline-danger w-100" type="submit">
                Cancelar cita
              </button>
            </form>
          </div>
        @endif

        @if($isClient && $nextVisit->status === 'pending')
          <p class="small text-muted mb-0">
            El agente te propuso esta fecha. Puedes confirmarla o cancelarla desde aquí.
          </p>
        @endif

        {{-- ACCIONES DEL AGENTE --}}
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
        {{-- NO HAY CITA PROGRAMADA --}}
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

      {{-- FORMULARIO PARA CREAR / REPROGRAMAR SOLO PARA EL AGENTE ---- --}}
      @if($canCreateVisit)
        <form method="POST"
              action="{{ route('chat.visits.store', $conversation) }}"
              id="chatVisitForm">
          @csrf

          {{-- Campo real que se envía al backend (Y-m-d H:i:s) --}}
          <input type="hidden" name="visit_date" id="chat_visit_datetime">

          <div class="mb-3">
            <label class="form-label text-uppercase small mb-1">Fecha y hora seleccionada</label>
            <p class="mb-1 small text-muted" id="chatVisitSummary">
              Aún no has seleccionado fecha ni horario.
            </p>
            <button type="button"
                    class="btn btn-outline-primary btn-sm"
                    id="chatVisitOpenBtn">
              Seleccionar fecha y hora
            </button>
          </div>

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

          @error('visit_date')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror

          <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="button"
                    class="btn btn-link btn-sm text-decoration-none"
                    id="chatVisitClearBtn">
              Borrar selección
            </button>
            <button class="btn btn-primary btn-sm"
                    type="submit"
                    id="chatVisitSubmitBtn"
                    disabled>
              Agendar visita
            </button>
          </div>
        </form>
      @endif
    </div>
  </div>

  {{-- MODAL PARA SELECCIONAR FECHA Y HORARIO (CUSTOM CALENDAR) ------------------------ --}}
  @if($canCreateVisit)
    <div id="chatVisitModal"
         style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
                background:rgba(0,0,0,.5);z-index:1200;overflow-y:auto;">
      <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                  max-width:720px;width:calc(100% - 32px);
                  max-height:calc(100vh - 80px);overflow:auto;
                  border-radius:16px;padding:24px;">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
          <div class="mb-3 mb-sm-0">
            <h2 class="h4 mb-1">Selecciona fecha y horario</h2>
            <p class="text-muted small mb-0">
              Primero elige un día y luego un bloque de una hora entre 8:00 y 20:00.
            </p>
          </div>
        </div>

        {{-- Custom Calendar Container --}}
        <div class="custom-calendar-container mb-3">
            <div class="calendar-header d-flex justify-content-between align-items-center mb-2">
                <button type="button" id="visit-prev-month" class="btn btn-sm btn-light">&lt;</button>
                <span id="visit-month-label" class="fw-bold"></span>
                <button type="button" id="visit-next-month" class="btn btn-sm btn-light">&gt;</button>
            </div>
            <div class="calendar-weekdays d-flex text-muted small mb-1">
                <div style="width: 14.28%; text-align: center;">Dom</div>
                <div style="width: 14.28%; text-align: center;">Lun</div>
                <div style="width: 14.28%; text-align: center;">Mar</div>
                <div style="width: 14.28%; text-align: center;">Mié</div>
                <div style="width: 14.28%; text-align: center;">Jue</div>
                <div style="width: 14.28%; text-align: center;">Vie</div>
                <div style="width: 14.28%; text-align: center;">Sáb</div>
            </div>
            <div id="visit-calendar-grid" class="calendar-grid d-flex flex-wrap">
                {{-- Days generated by JS --}}
            </div>
        </div>

        <div class="mt-3">
          <p class="text-muted small mb-2">
            Horarios disponibles (bloques de 1 hora, de 8:00 a 20:00):
          </p>
          <div id="chatVisitSlotsGrid" class="row g-2">
            {{-- botones generados por JS --}}
          </div>
          <p id="chatVisitNoSlotsMsg" class="text-xs text-danger mt-2 d-none">
            No hay horarios disponibles para esta fecha. Prueba con otro día.
          </p>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <button type="button"
                  class="btn btn-link btn-sm text-decoration-none"
                  id="chatVisitModalCancel">
            Cancelar
          </button>
          <button type="button"
                  class="btn btn-success btn-sm"
                  id="chatVisitModalConfirm"
                  disabled>
            Confirmar horario
          </button>
        </div>
      </div>
    </div>

    <script>
      (function () {
        // === Configuración base ===
        const VISIT_SLOT_HOURS = [
          "08:00","09:00","10:00","11:00","12:00",
          "13:00","14:00","15:00","16:00","17:00","18:00","19:00"
        ];
        const MONTH_NAMES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        const visitSlots = @json($visitSlots ?? []);

        // Elementos principales
        const openBtn    = document.getElementById('chatVisitOpenBtn');
        const clearBtn   = document.getElementById('chatVisitClearBtn');
        const submitBtn  = document.getElementById('chatVisitSubmitBtn');
        const summaryEl  = document.getElementById('chatVisitSummary');
        const hiddenDate = document.getElementById('chat_visit_datetime');
        const form       = document.getElementById('chatVisitForm');

        // Elementos del modal
        const modal      = document.getElementById('chatVisitModal');
        const grid       = document.getElementById('chatVisitSlotsGrid');
        const msgNoSlots = document.getElementById('chatVisitNoSlotsMsg');
        const modalCancelBtn  = document.getElementById('chatVisitModalCancel');
        const modalConfirmBtn = document.getElementById('chatVisitModalConfirm');

        // Calendar Elements
        const calGrid    = document.getElementById('visit-calendar-grid');
        const monthLabel = document.getElementById('visit-month-label');
        const prevBtn    = document.getElementById('visit-prev-month');
        const nextBtn    = document.getElementById('visit-next-month');

        if (!openBtn || !modal) return;

        let currentDate  = new Date(); // Para navegación
        let selectedDate = null;       // "YYYY-MM-DD"
        let selectedTime = null;       // "HH:MM"

        const toIsoDate = (d) => {
            return d.getFullYear() + '-' +
                   String(d.getMonth() + 1).padStart(2, '0') + '-' +
                   String(d.getDate()).padStart(2, '0');
        };

        const renderCalendar = () => {
            calGrid.innerHTML = '';
            const year  = currentDate.getFullYear();
            const month = currentDate.getMonth();

            monthLabel.textContent = `${MONTH_NAMES[month]} ${year}`;

            const firstDay = new Date(year, month, 1);
            const lastDay  = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = firstDay.getDay(); // 0 = Domingo

            // Padding days (previous month)
            for (let i = 0; i < startDay; i++) {
                const pad = document.createElement('div');
                pad.style.width = '14.28%';
                pad.style.height = '40px';
                calGrid.appendChild(pad);
            }

            // Days
            const todayIso = toIsoDate(new Date());

            for (let d = 1; d <= daysInMonth; d++) {
                const dateObj = new Date(year, month, d);
                const iso = toIsoDate(dateObj);
                
                const el = document.createElement('div');
                el.textContent = d;
                el.className = 'custom-day';
                el.style.width = '14.28%';
                el.style.height = '40px';
                el.style.display = 'flex';
                el.style.alignItems = 'center';
                el.style.justifyContent = 'center';
                el.style.cursor = 'pointer';
                el.style.borderRadius = '50%';

                // Styles logic
                if (iso < todayIso) {
                    el.classList.add('text-muted');
                    el.style.cursor = 'not-allowed';
                    el.style.opacity = '0.5';
                } else {
                    el.addEventListener('click', () => selectDate(iso, el));
                }

                if (iso === selectedDate) {
                    el.classList.add('bg-primary', 'text-white');
                }

                calGrid.appendChild(el);
            }
        };

        const selectDate = (iso, el) => {
            // Remove active class from others
            Array.from(calGrid.children).forEach(c => c.classList.remove('bg-primary', 'text-white'));
            el.classList.add('bg-primary', 'text-white');
            
            selectedDate = iso;
            renderSlots(iso);
        };

        const renderSlots = (dateStr) => {
          grid.innerHTML = '';
          selectedTime = null;
          modalConfirmBtn.disabled = true;

          const booked = new Set((visitSlots[dateStr] || []).map(t => t.slice(0,5)));
          let availableCount = 0;

          VISIT_SLOT_HOURS.forEach(time => {
            const col = document.createElement('div');
            col.className = 'col-6';

            const btn = document.createElement('button');
            btn.type = 'button';

            const startHour = parseInt(time.slice(0, 2), 10);
            const endHour   = startHour + 1;
            const label     = `${startHour}:00 - ${endHour}:00`;

            btn.textContent = label;
            btn.className = 'btn btn-outline-secondary w-100 text-start';

            if (booked.has(time)) {
              btn.disabled = true;
              btn.classList.add('disabled');
            } else {
              availableCount++;
              btn.addEventListener('click', () => {
                Array.from(grid.querySelectorAll('button')).forEach(b => {
                  b.classList.remove('btn-primary','text-white');
                  b.classList.add('btn-outline-secondary');
                });
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary','text-white');
                selectedTime = time;
                modalConfirmBtn.disabled = false;
              });
            }

            col.appendChild(btn);
            grid.appendChild(col);
          });

          if (msgNoSlots) {
            msgNoSlots.classList.toggle('d-none', availableCount > 0);
          }
        };

        const clearSelection = () => {
          selectedDate = null;
          selectedTime = null;
          hiddenDate.value = '';
          summaryEl.textContent = 'Aún no has seleccionado fecha ni horario.';
          submitBtn.disabled = true;
          grid.innerHTML = '';
          if (msgNoSlots) msgNoSlots.classList.add('d-none');
          renderCalendar();
        };

        const openModal = () => {
          modal.style.display = 'block';
          document.body.classList.add('overflow-hidden');
          renderCalendar();
        };

        const closeModal = () => {
          modal.style.display = 'none';
          document.body.classList.remove('overflow-hidden');
        };

        // Navigation
        prevBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        nextBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        // Abrir modal
        openBtn.addEventListener('click', openModal);

        // Cancelar en modal
        modalCancelBtn.addEventListener('click', closeModal);

        // Confirmar horario desde el modal
        modalConfirmBtn.addEventListener('click', () => {
          if (!selectedDate || !selectedTime) {
            alert('Selecciona una fecha y un horario disponible.');
            return;
          }

          hiddenDate.value = `${selectedDate} ${selectedTime}:00`;
          const [y,m,d] = selectedDate.split('-');
          const startHour = parseInt(selectedTime.slice(0,2), 10);
          const endHour   = startHour + 1;

          summaryEl.textContent =
            `Visita el ${d}/${m}/${y} de ${startHour}:00 a ${endHour}:00`;

          submitBtn.disabled = false;
          closeModal();
        });

        // Borrar selección desde la tarjeta
        clearBtn.addEventListener('click', (e) => {
          e.preventDefault();
          clearSelection();
        });

        // Cerrar el modal haciendo click fuera
        window.addEventListener('click', (e) => {
          if (e.target === modal) {
            closeModal();
          }
        });

        // Cerrar con Escape
        window.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
          }
        });

        // Validar antes de enviar por si acaso
        form.addEventListener('submit', (e) => {
          if (!hiddenDate.value) {
            e.preventDefault();
            alert('Selecciona primero una fecha y un horario para la visita.');
          }
        });
      })();
    </script>
  @endif
@endif


{{-- ==================== RESERVAS (RENTAS) ==================== --}}
@if($property && $property->listing_type === 'rent')
  @php
      // Mismos rangos bloqueados que en show.blade
      $blocked = [];

      if ($property->status === 'rented' && $property->rented_until) {
          $blocked[] = [
              'from' => now()->format('Y-m-d'),
              'to'   => $property->rented_until->copy()->subDay()->format('Y-m-d'),
          ];
      }

      $reservas = $property->reservations()
          ->whereIn('status', ['paid','confirmed','completed'])
          ->orderBy('start_date')
          ->get();

      foreach ($reservas as $r) {
          $from = \Illuminate\Support\Carbon::parse($r->start_date)->format('Y-m-d');
          $to   = \Illuminate\Support\Carbon::parse($r->end_date)->subDay()->format('Y-m-d');
          $blocked[] = ['from' => $from, 'to' => $to];
      }
  @endphp

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
        <p class="text-muted small mb-2">
          Selecciona tus fechas con el mismo calendario que en la ficha de la propiedad.
        </p>
        <button type="button" class="btn btn-primary w-100" id="chat-open-reservation">
          Elegir fechas
        </button>
      @else
        <p class="text-muted mb-0">
          El cliente aún no ha generado una reservación.
        </p>
      @endif
    </div>
  </div>

  {{-- Modal de reserva (igual lógica que en show.blade, adaptado al chat) --}}
  @if($isClient)
    <div id="chat-reservation-modal"
         style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1100;overflow-y:auto;">
      <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                  max-width:720px;width:calc(100% - 32px);max-height:calc(100vh - 80px);overflow:auto;
                  border-radius:16px;padding:24px;">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3">
          <div class="mb-3 mb-sm-0">
            <h2 id="chat-modalNightCount" class="h4 mb-1">Selecciona fechas</h2>
            <p id="chat-modalDateHint" class="text-muted small mb-0">Agrega tus fechas de viaje</p>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <div class="border rounded-3 p-2" style="width:140px;">
              <label class="text-uppercase text-muted small mb-0">Llegada</label>
              <input id="chat-modalStartDate" class="form-control form-control-sm border-0 p-0" placeholder="dd/mm/aaaa" readonly>
            </div>
            <div class="border rounded-3 p-2" style="width:140px;">
              <label class="text-uppercase text-muted small mb-0">Salida</label>
              <input id="chat-modalEndDate" class="form-control form-control-sm border-0 p-0" placeholder="dd/mm/aaaa" readonly>
            </div>
          </div>
        </div>

        <div class="fp-shell mb-3">
          <input id="chat-dateRange" class="fp-hidden-input" aria-hidden="true" tabindex="-1"/>
        </div>

        <div id="chat-priceSummary" class="mt-3 p-3 bg-light rounded-3 border d-none">
          <p class="fw-semibold mb-1">Resumen de reserva:</p>
          <p id="chat-nightsCount" class="mb-0 small">Noches: 0</p>
          <p id="chat-totalPrice" class="mb-0 small">Total: $0.00 MXN</p>
        </div>

        <p class="text-muted small mt-2 mb-0">
          * Los días no disponibles aparecen deshabilitados.
        </p>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <button id="chat-clearDatesBtn" type="button" class="btn btn-link btn-sm text-decoration-none">
            Borrar fechas
          </button>
          <div class="d-flex gap-2">
            <button type="button" id="chat-cancelReservationBtn" class="btn btn-outline-secondary btn-sm">
              Cancelar
            </button>
            <button type="button" id="chat-confirmReservationBtn" class="btn btn-success btn-sm" disabled>
              Confirmar reserva
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- Formulario oculto para enviar la reserva al backend del chat --}}
    <form id="chatReservationForm"
          method="POST"
          action="{{ route('chat.reservations.store', $conversation) }}"
          class="d-none">
      @csrf
      <input type="hidden" name="start_date" id="chat_res_start">
      <input type="hidden" name="end_date" id="chat_res_end">
    </form>

    <script>
      (function () {
        const propertyPrice   = {{ $property->price }};
        const blockedRanges   = @json($blocked);

        const openBtn         = document.getElementById('chat-open-reservation');
        const modal           = document.getElementById('chat-reservation-modal');
        const startInput      = document.getElementById('chat-modalStartDate');
        const endInput        = document.getElementById('chat-modalEndDate');
        const nightTitle      = document.getElementById('chat-modalNightCount');
        const dateHint        = document.getElementById('chat-modalDateHint');
        const summaryBox      = document.getElementById('chat-priceSummary');
        const nightsCountEl   = document.getElementById('chat-nightsCount');
        const totalPriceEl    = document.getElementById('chat-totalPrice');
        const clearBtn        = document.getElementById('chat-clearDatesBtn');
        const cancelBtn       = document.getElementById('chat-cancelReservationBtn');
        const confirmBtn      = document.getElementById('chat-confirmReservationBtn');
        const hiddenStart     = document.getElementById('chat_res_start');
        const hiddenEnd       = document.getElementById('chat_res_end');
        const form            = document.getElementById('chatReservationForm');

        if (!openBtn || !modal) return;

        let fpInstance = null;
        let selectedRange = { start: null, end: null };

        const formatDate = (date) => {
          if (!date) return '';
          const d  = new Date(date);
          const dd = String(d.getDate()).padStart(2,'0');
          const mm = String(d.getMonth()+1).padStart(2,'0');
          const yy = d.getFullYear();
          return `${dd}/${mm}/${yy}`;
        };

        const isoDate = (date) => {
          const d = new Date(date);
          return d.toISOString().split('T')[0];
        };

        const openModal = () => {
          modal.style.display = 'block';
          document.body.classList.add('overflow-hidden');
          if (!fpInstance && window.flatpickr) {
            initCalendar();
          }
        };

        const closeModal = () => {
          modal.style.display = 'none';
          document.body.classList.remove('overflow-hidden');
        };

        const initCalendar = () => {
          const shell = modal.querySelector('.fp-shell');
          const disable = blockedRanges.map(r => ({ from: r.from, to: r.to }));

          // Locale oficial ES
          let esLocale = (window.flatpickr.l10ns && window.flatpickr.l10ns.es)
            ? window.flatpickr.l10ns.es
            : {
                weekdays: {
                  shorthand: ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'],
                  longhand:  ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
                },
                months: {
                  shorthand: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                  longhand:  ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                },
                firstDayOfWeek: 1,
                rangeSeparator: ' a ',
              };

          esLocale.firstDayOfWeek = 1;
          esLocale.rangeSeparator = esLocale.rangeSeparator || ' a ';

          fpInstance = flatpickr('#chat-dateRange', {
            mode: 'range',
            inline: true,
            static: true,
            appendTo: shell,
            minDate: 'today',
            dateFormat: 'Y-m-d',
            altInput: false,
            showMonths: window.innerWidth >= 640 ? 2 : 1,
            disableMobile: true,
            disable,
            weekNumbers: false,
            locale: esLocale,
            onChange: (dates) => {
              if (dates.length === 1) {
                selectedRange = { start: dates[0], end: null };
                startInput.value = formatDate(dates[0]);
                endInput.value   = '';
                nightTitle.textContent = 'Selecciona fecha de salida';
                dateHint.textContent   = 'Agrega tu fecha de salida';
                summaryBox.classList.add('d-none');
                confirmBtn.disabled = true;
              } else if (dates.length === 2) {
                const [start, end] = dates.sort((a,b) => a - b);
                selectedRange = { start, end };
                const ms = 1000 * 60 * 60 * 24;
                const nights = Math.round((end - start) / ms);
                const total  = (nights * propertyPrice).toFixed(2);

                startInput.value = formatDate(start);
                endInput.value   = formatDate(end);
                nightTitle.textContent = `${nights} noche${nights > 1 ? 's' : ''}`;
                dateHint.textContent   = `${formatDate(start)} - ${formatDate(end)}`;

                nightsCountEl.textContent = 'Noches: ' + nights;
                totalPriceEl.textContent  = 'Total: $' + total + ' MXN';
                summaryBox.classList.remove('d-none');
                confirmBtn.disabled = false;
              } else {
                selectedRange = { start: null, end: null };
                startInput.value = '';
                endInput.value   = '';
                nightTitle.textContent = 'Selecciona fechas';
                dateHint.textContent   = 'Agrega tus fechas de viaje';
                summaryBox.classList.add('d-none');
                confirmBtn.disabled = true;
              }
            },
          });

          window.addEventListener('resize', () => {
            if (!fpInstance) return;
            const months = window.innerWidth >= 640 ? 2 : 1;
            fpInstance.set('showMonths', months);
          });
        };

        openBtn.addEventListener('click', openModal);

        clearBtn.addEventListener('click', () => {
          if (!fpInstance) return;
          fpInstance.clear();
          selectedRange = { start: null, end: null };
          startInput.value = '';
          endInput.value   = '';
          nightTitle.textContent = 'Selecciona fechas';
          dateHint.textContent   = 'Agrega tus fechas de viaje';
          summaryBox.classList.add('d-none');
          confirmBtn.disabled = true;
        });

        cancelBtn.addEventListener('click', closeModal);

        confirmBtn.addEventListener('click', () => {
          if (!selectedRange.start || !selectedRange.end) {
            alert('Selecciona fechas de entrada y salida');
            return;
          }
          hiddenStart.value = isoDate(selectedRange.start);
          hiddenEnd.value   = isoDate(selectedRange.end);
          form.submit();
        });

        // Cerrar al hacer click fuera del contenido
        window.addEventListener('click', (e) => {
          if (e.target === modal) {
            closeModal();
          }
        });

        // Cerrar con Escape
        window.addEventListener('keydown', (e) => {
          if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
          }
        });
      })();
    </script>
  @endif
@endif
