<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitar reservación</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- (Opcional) El kit de FontAwesome te estaba dando 403; puedes quitarlo mientras pruebas --}}
  {{-- <script src="https://kit.fontawesome.com/a2e0ad5d8a.js" crossorigin="anonymous"></script> --}}

  <script
  src="https://www.paypal.com/sdk/js?client-id={{ urlencode(config('services.paypal.client_id')) }}&currency={{ config('services.paypal.currency','MXN') }}&intent=capture&components=buttons&debug=true"
  data-sdk-integration-source="button-factory"
></script>


<script>
  // Para ver en consola si el ID llega desde Blade (no imprime el secreto)
  console.log('PAYPAL_CLIENT_ID (inicio):', '{{ substr(env('PAYPAL_CLIENT_ID'), 0, 8) }}…');
</script>
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="max-w-6xl mx-auto px-4 py-6">
    <a href="{{ route('properties.show', $property) }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-6">
      {{-- <i class="fa-solid fa-arrow-left mr-2"></i> --}}
      ← Volver a la propiedad
    </a>

    <h1 class="text-2xl md:text-3xl font-bold mb-6">Solicitar reservación</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Columna izquierda: Pasos --}}
      <div class="lg:col-span-2 space-y-4">

        {{-- Paso 1: cuándo pagar --}}
        <div class="bg-white rounded-xl shadow p-5">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">1. Elige cuándo quieres pagar</h2>
          </div>
          <div class="mt-4 space-y-3">
            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer">
              <div>
                <div class="font-medium">Paga ${{ number_format($total, 2) }} MXN ahora</div>
                <div class="text-sm text-gray-500">Pago completo hoy</div>
              </div>
              <input type="radio" name="pay_mode" value="full" class="w-5 h-5" checked>
            </label>
            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer">
              <div>
                <div class="font-medium">
                  Paga una parte ahora y otra más adelante
                  <span class="text-gray-700">(${{ number_format($total * 0.20, 2) }} MXN ahora)</span>
                </div>
                <div class="text-sm text-gray-500">20% hoy, el resto antes de la fecha</div>
              </div>
              <input type="radio" name="pay_mode" value="partial" class="w-5 h-5">
            </label>
          </div>
          <div class="mt-4 text-right">
            <button id="goto-step-2" class="px-5 py-2.5 bg-black text-white rounded-lg">Siguiente</button>
          </div>
        </div>

        {{-- Paso 2: forma de pago (PayPal) --}}
        <div id="step-2" class="bg-white rounded-xl shadow p-5 opacity-50 pointer-events-none">
          <h2 class="text-lg font-semibold">2. Agrega una forma de pago</h2>
          <p class="text-sm text-gray-500 mt-1">Usaremos PayPal para procesar tu pago de forma segura.</p>
          <div class="mt-4">
            <div id="paypal-button-container"></div>
            <p id="pay-help" class="text-xs text-gray-500 mt-2">Selecciona el modo de pago en el Paso 1 y luego paga con PayPal.</p>
          </div>
        </div>

        {{-- Paso 3: mensaje al anfitrión --}}
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-lg font-semibold">3. Escribe un mensaje para el anfitrión</h2>
          <textarea id="hostMessage" class="mt-3 w-full border rounded-lg p-3" rows="4" placeholder="Cuéntale al anfitrión algo sobre tu estancia (opcional)"></textarea>
          <p class="text-xs text-gray-400 mt-2">Este mensaje es opcional y no afecta el pago.</p>
        </div>

        {{-- Paso 4: revisión final --}}
        <div class="bg-white rounded-xl shadow p-5">
          <h2 class="text-lg font-semibold">4. Revisa tu solicitud</h2>
          <ul class="mt-3 text-sm text-gray-700 space-y-1">
            <li><b>Fechas:</b> {{ \Carbon\Carbon::parse($reservation->start_date)->locale('es')->isoFormat('D [de] MMM') }} — {{ \Carbon\Carbon::parse($reservation->end_date)->locale('es')->isoFormat('D [de] MMM YYYY') }} ({{ $reservation->nights }} noches)</li>
            <li><b>Participantes:</b> {{ $reservation->guests ?? 1 }}</li>
            <li><b>Política de cancelación:</b> Cancelación gratuita hasta 7 días antes (ejemplo)</li>
          </ul>
        </div>
      </div>

      {{-- Columna derecha: resumen --}}
      <aside class="bg-white rounded-xl shadow p-5 h-fit">
        <div class="flex gap-3">
          @php $thumb = $property->images->first()->image_path ?? null; @endphp
          <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100">
            @if($thumb)
              <img src="{{ asset('storage/'.$thumb) }}" class="w-full h-full object-cover" alt="Imagen">
            @endif
          </div>
          <div class="flex-1">
            <div class="text-sm text-gray-500">Alojamiento</div>
            <div class="font-semibold line-clamp-2">{{ $property->title }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $property->location ?? $property->city }}</div>
          </div>
        </div>

        <div class="mt-5 border-t pt-4 space-y-2 text-sm">
          <div class="flex justify-between"><span>Precio x noche</span> <span>${{ number_format($price,2) }} MXN</span></div>
          <div class="flex justify-between"><span>{{ $reservation->nights }} noches</span> <span>${{ number_format($subtotal,2) }} MXN</span></div>
          <div class="flex justify-between"><span>Tarifa de servicio (5%)</span> <span>${{ number_format($serviceFee,2) }} MXN</span></div>
          <div class="flex justify-between"><span>Impuestos (16%)</span> <span>${{ number_format($taxes,2) }} MXN</span></div>
          <div class="flex justify-between font-semibold text-lg border-t pt-3"><span>Total MXN</span> <span>${{ number_format($total,2) }} MXN</span></div>
        </div>

        <div class="mt-4 text-xs text-gray-500">
          ** El cargo final lo verás al pagar con PayPal.
        </div>
      </aside>
    </div>
  </div>

  <script>
  (function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const reservationId = {{ $reservation->id }};
    const step2 = document.getElementById('step-2');

    // Evita render múltiple del botón
    let paypalRendered = false;

    // Lee modo de pago seleccionado del paso 1
    function currentPayMode(){
      const r = document.querySelector('input[name="pay_mode"]:checked');
      return r ? r.value : 'full';
    }

    // Render seguro del botón PayPal (espera al SDK)
    function renderPayPalButtons() {
      if (paypalRendered) return;               // ya está
      if (!window.paypal || !paypal.Buttons) {  // espera SDK
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
                pay_mode: (document.querySelector('input[name="pay_mode"]:checked')?.value || 'full')
            })
            })
            .then(r => r.json())
            .then(j => {
            if (j.error) throw new Error(j.error);
            if (!j.id) throw new Error('No se recibió ID de PayPal');
            return j.id;
            });
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
            if (!(j.success || j.already)) throw new Error(j.error || 'Fallo al capturar');
            alert('¡Pago exitoso! Tu reserva quedó confirmada.');
            window.location.href = "{{ route('visits.my') }}";
            })
            .catch(err => {
            console.error(err);
            alert('Ocurrió un problema al confirmar el pago.');
            });
        },

        onError: function(err) {
            console.error(err);
            alert('Error con PayPal. Intenta de nuevo.');
        }
        }).render('#paypal-button-container');
    }

    // Activar paso 2 y renderizar botones cuando den “Siguiente”
    document.getElementById('goto-step-2').addEventListener('click', () => {
      step2.classList.remove('opacity-50','pointer-events-none');
      step2.scrollIntoView({ behavior:'smooth', block:'start' });
      renderPayPalButtons();
    });

    // Por si el usuario recarga y ya está en paso 2 visible:
    window.addEventListener('load', () => {
      if (!step2.classList.contains('pointer-events-none')) {
        renderPayPalButtons();
      }
    });
  })();
  </script>
</body>
</html>
