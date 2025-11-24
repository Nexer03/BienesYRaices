<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitar reservación</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Anti-flash: aplica tema guardado ANTES de pintar la página --}}
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

  {{-- Tailwind --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>

  {{-- Iconos para el botón de tema --}}
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  {{-- SDK de PayPal --}}
  <script
    src="https://www.paypal.com/sdk/js?client-id={{ urlencode(config('services.paypal.client_id')) }}&currency={{ config('services.paypal.currency','MXN') }}&intent=capture&components=buttons&debug=true"
    data-sdk-integration-source="button-factory">
  </script>

  <style>
    /* Fade suave al cambiar de tema */
    html.theme-fade * {
      transition:
        background-color .35s ease,
        color .35s ease,
        border-color .35s ease,
        fill .35s ease;
    }

    /* Botón de tema + animaciones */
    #theme-toggle {
      transition:
        background-color .25s ease,
        color .25s ease,
        transform .25s ease,
        box-shadow .25s ease;
    }

    #theme-toggle.theme-bounce {
      transform: translateY(-1px) scale(1.03);
      box-shadow: 0 15px 30px rgba(0,0,0,.18);
    }

    #theme-toggle-icon {
      transition: transform .35s ease, opacity .2s ease;
    }

    #theme-toggle-icon.theme-spin {
      transform: rotate(180deg);
    }
  </style>
</head>

<body class="min-h-screen bg-gray-50 dark:bg-slate-950 text-gray-800 dark:text-gray-100 transition-colors duration-300">

  {{-- Botón flotante de modo oscuro --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <div class="max-w-6xl mx-auto px-4 py-6">

    <a href="{{ route('properties.show', $property) }}"
       class="inline-flex items-center text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 mb-6">
      ← Volver a la propiedad
    </a>

    <h1 class="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-50">
      Solicitar reservación
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- COLUMNA IZQUIERDA --}}
      <div class="lg:col-span-2 space-y-4">

        {{-- PASO 1 --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-800">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50">1. Confirmar monto a pagar</h2>

          <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
            Revisa que el total a pagar sea correcto antes de continuar.
          </p>

          <div class="mt-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="font-medium text-lg text-gray-900 dark:text-gray-50">
              Total a pagar: ${{ number_format($total ?? $reservation->total_price, 2) }} MXN
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Pago completo — pago único hoy.
            </div>
          </div>

          <div class="mt-4 text-right">
            <button id="goto-step-2"
                    class="px-5 py-2.5 bg-black text-white rounded-lg hover:bg-gray-900">
              Continuar
            </button>
          </div>
        </div>

        {{-- PASO 2 --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-800">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50">2. Escribe un mensaje para el anfitrión</h2>

          <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
            El anfitrión recibirá este mensaje junto con tu reservación.
          </p>

          <textarea id="hostMessage"
                    class="mt-3 w-full border border-gray-300 dark:border-gray-700 rounded-lg p-3
                           bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100"
                    rows="4"
                    placeholder="Mensaje opcional"></textarea>
        </div>

        {{-- PASO 3 --}}
        <div id="step-3"
             class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-800
                    opacity-50 pointer-events-none">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50">3. Realizar pago</h2>

          <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
            Paga de forma segura con PayPal.
          </p>

          <div class="mt-4">
            <div id="paypal-button-container"></div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
              Se confirmará tu reservación automáticamente después del pago.
            </p>
          </div>
        </div>

        {{-- PASO 4 --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-800">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50">4. Información de la reservación</h2>

          <ul class="mt-3 text-sm text-gray-700 dark:text-gray-200 space-y-1">
            <li>
              <b>Fechas:</b>
              {{ \Carbon\Carbon::parse($reservation->start_date)->locale('es')->isoFormat('D [de] MMM YYYY') }}
              —
              {{ \Carbon\Carbon::parse($reservation->end_date)->locale('es')->isoFormat('D [de] MMM YYYY') }}
              ({{ $reservation->nights }} noches)
            </li>

            <!-- <li><b>Participantes:</b> {{ $reservation->guests ?? 1 }}</li> -->
            <li><b>Total a pagar:</b> ${{ number_format($total,2) }} MXN</li>

            <li><b>Reserva #:</b> {{ $reservation->id }}</li>

            <li>
              <b>Mensaje al anfitrión:</b>
              <span class="text-gray-500 dark:text-gray-400">(Lo verás reflejado al confirmar)</span>
            </li>
          </ul>
        </div>

      </div>

      {{-- RESUMEN DERECHA --}}
      <aside class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 h-fit border border-gray-100 dark:border-gray-800">

        {{-- Imagen + info --}}
        <div class="flex gap-3">
          @php $thumb = $property->images->first()->image_path ?? null; @endphp

          <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
            @if($thumb)
              <img src="{{ asset('storage/'.$thumb) }}" class="w-full h-full object-cover">
            @endif
          </div>

          <div class="flex-1">
            <div class="text-sm text-gray-500 dark:text-gray-400">Alojamiento</div>
            <div class="font-semibold text-gray-900 dark:text-gray-50">{{ $property->title }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ $property->location ?? $property->city }}
            </div>
          </div>
        </div>

        {{-- Resumen de costos --}}
        <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 text-sm text-gray-800 dark:text-gray-100">

          <div class="flex justify-between">
            <span>Precio x noche</span>
            <span>${{ number_format($price,2) }} MXN</span>
          </div>

          <div class="flex justify-between">
            <span>{{ $reservation->nights }} noches</span>
            <span>${{ number_format($subtotal,2) }} MXN</span>
          </div>

          <!--
          <div class="flex justify-between">
            <span>Comisión del agente ({{ number_format($commissionPercent,2) }}%)</span>
            <span>${{ number_format($agentCommission,2) }} MXN</span>
          </div>

          <div class="flex justify-between">
            <span>Ganancia del agente</span>
            <span>${{ number_format($agentEarnings,2) }} MXN</span>
          </div>

          <div class="flex justify-between">
            <span>Ganancia de la plataforma</span>
            <span>${{ number_format($platformEarnings,2) }} MXN</span>
          </div>
          -->
          <div class="flex justify-between font-semibold text-lg border-t border-gray-200 dark:border-gray-700 pt-3">
            <span>Total MXN</span>
            <span>${{ number_format($total,2) }} MXN</span>
          </div>

        </div>
      </aside>

    </div>
  </div>

<script>
(function () {

  const step3 = document.getElementById('step-3');
  let paypalRendered = false;

  function renderPayPalButtons() {
    if (paypalRendered) return;
    if (!window.paypal || !paypal.Buttons) {
      return setTimeout(renderPayPalButtons, 150);
    }

    paypalRendered = true;

    paypal.Buttons({
      createOrder: function () {
        return fetch("{{ route('paypal.createOrder') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            reservation_id: {{ $reservation->id }},
            pay_mode: 'full',
            host_message: document.getElementById('hostMessage').value || null
          })
        })
        .then(r => r.json())
        .then(j => j.id);
      },

      onApprove: function(data) {
        return fetch("{{ route('paypal.captureOrder') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ order_id: data.orderID })
        })
        .then(r => r.json())
        .then(j => {
          alert('¡Pago exitoso! Tu reservación quedó confirmada.');
          window.location.href = "{{ route('visits.my') }}";
        });
      }

    }).render('#paypal-button-container');
  }

  document.getElementById('goto-step-2').addEventListener('click', () => {
    step3.classList.remove('opacity-50','pointer-events-none');
    step3.scrollIntoView({ behavior:'smooth' });
    renderPayPalButtons();
  });

})();
</script>

{{-- Toggle de tema con animación (igual que en la vista de propiedad) --}}
<script>
  (function () {
    const html  = document.documentElement;
    const btn   = document.getElementById('theme-toggle');
    const icon  = document.getElementById('theme-toggle-icon');
    const label = btn?.querySelector('span');

    function setIconAndLabel() {
      const isDark = html.classList.contains('dark');
      if (!icon || !label) return;

      icon.classList.remove('fa-sun', 'fa-moon');
      icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
      label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
    }

    function startPageFade() {
      html.classList.add('theme-fade');
      setTimeout(() => html.classList.remove('theme-fade'), 400);
    }

    function animateButton() {
      if (!btn || !icon) return;
      btn.classList.add('theme-bounce');
      icon.classList.add('theme-spin');
      setTimeout(() => {
        btn.classList.remove('theme-bounce');
        icon.classList.remove('theme-spin');
      }, 350);
    }

    function apply(mode) {
      const isDark = mode === 'dark';
      startPageFade();
      html.classList.toggle('dark', isDark);
      try { localStorage.setItem('theme', mode); } catch (e) {}
      setIconAndLabel();
      animateButton();
    }

    // Estado inicial según clase actual del <html>
    setIconAndLabel();

    btn?.addEventListener('click', () => {
      const next = html.classList.contains('dark') ? 'light' : 'dark';
      apply(next);
    });
  })();
</script>

</body>
</html>
