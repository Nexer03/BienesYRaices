<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitar reservación</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>

  <script
    src="https://www.paypal.com/sdk/js?client-id={{ urlencode(config('services.paypal.client_id')) }}&currency={{ config('services.paypal.currency','MXN') }}&intent=capture&components=buttons&debug=true"
    data-sdk-integration-source="button-factory">
  </script>
</head>

<body class="bg-gray-50 text-gray-800">
  <div class="max-w-6xl mx-auto px-4 py-6">

    <a href="{{ route('properties.show', $property) }}"
       class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6">
      ← Volver a la propiedad
    </a>

    <h1 class="text-2xl md:text-3xl font-bold mb-6">Solicitar reservación</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- COLUMNA IZQUIERDA --}}
      <div class="lg:col-span-2 space-y-4">

        {{-- PASO 1 --}}
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-lg font-semibold">1. Confirmar monto a pagar</h2>

          <p class="text-sm text-gray-600 mt-2">
            Revisa que el total a pagar sea correcto antes de continuar.
          </p>

          <div class="mt-4 p-4 border rounded-lg bg-gray-50">
            <div class="font-medium text-lg">
              Total a pagar: ${{ number_format($total ?? $reservation->total_price, 2) }} MXN
            </div>
            <div class="text-sm text-gray-500 mt-1">
              Pago completo — pago único hoy.
            </div>
          </div>

          <div class="mt-4 text-right">
            <button id="goto-step-2" class="px-5 py-2.5 bg-black text-white rounded-lg">
              Continuar
            </button>
          </div>
        </div>

        {{-- PASO 2 --}}
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-lg font-semibold">2. Escribe un mensaje para el anfitrión</h2>

          <p class="text-sm text-gray-600 mt-1">
            El anfitrión recibirá este mensaje junto con tu reservación.
          </p>

          <textarea id="hostMessage"
                    class="mt-3 w-full border rounded-lg p-3"
                    rows="4"
                    placeholder="Mensaje opcional"></textarea>
        </div>

        {{-- PASO 3 --}}
        <div id="step-3" class="bg-white rounded-xl shadow p-5 opacity-50 pointer-events-none">
          <h2 class="text-lg font-semibold">3. Realizar pago</h2>

          <p class="text-sm text-gray-600 mt-1">
            Paga de forma segura con PayPal.
          </p>

          <div class="mt-4">
            <div id="paypal-button-container"></div>
            <p class="text-xs text-gray-500 mt-2">
              Se confirmará tu reservación automáticamente después del pago.
            </p>
          </div>
        </div>

        {{-- PASO 4 --}}
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-lg font-semibold">4. Información de la reservación</h2>

          <ul class="mt-3 text-sm text-gray-700 space-y-1">
            <li>
              <b>Fechas:</b>
              {{ \Carbon\Carbon::parse($reservation->start_date)->locale('es')->isoFormat('D [de] MMM YYYY') }}
              —
              {{ \Carbon\Carbon::parse($reservation->end_date)->locale('es')->isoFormat('D [de] MMM YYYY') }}
              ({{ $reservation->nights }} noches)
            </li>

            <li><b>Participantes:</b> {{ $reservation->guests ?? 1 }}</li>
            <li><b>Total a pagar:</b> ${{ number_format($total,2) }} MXN</li>

            <li><b>Reserva #:</b> {{ $reservation->id }}</li>

            <li>
              <b>Mensaje al anfitrión:</b>
              <span class="text-gray-500">(Lo verás reflejado al confirmar)</span>
            </li>
          </ul>
        </div>

      </div>

      {{-- RESUMEN DERECHA --}}
      <aside class="bg-white rounded-xl shadow p-5 h-fit">

        {{-- Imagen + info --}}
        <div class="flex gap-3">
          @php $thumb = $property->images->first()->image_path ?? null; @endphp

          <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
            @if($thumb)
              <img src="{{ asset('storage/'.$thumb) }}" class="w-full h-full object-cover">
            @endif
          </div>

          <div class="flex-1">
            <div class="text-sm text-gray-500">Alojamiento</div>
            <div class="font-semibold">{{ $property->title }}</div>
            <div class="text-xs text-gray-500 mt-1">
              {{ $property->location ?? $property->city }}
            </div>
          </div>
        </div>

        {{-- Resumen de costos --}}
        <div class="mt-5 border-t pt-4 space-y-2 text-sm">

          <div class="flex justify-between">
            <span>Precio x noche</span>
            <span>${{ number_format($price,2) }} MXN</span>
          </div>

          <div class="flex justify-between">
            <span>{{ $reservation->nights }} noches</span>
            <span>${{ number_format($subtotal,2) }} MXN</span>
          </div>

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

          <div class="flex justify-between font-semibold text-lg border-t pt-3">
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

</body>
</html>
