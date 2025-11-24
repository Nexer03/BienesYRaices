<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar comisión - SIN BECA NO HAY RENTA</title>

    {{-- Anti-flash --}}
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { darkMode: 'class' };</script>

    {{-- Iconos --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body
    class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">

<x-main-header />

<main class="flex-grow max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-3xl font-bold mb-8 flex items-center gap-3 text-gray-900 dark:text-gray-100">
        <i class="fa-solid fa-money-check-dollar text-blue-500"></i>
        Pagar comisión de venta
    </h1>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/40 border border-green-300 dark:border-green-700
                    text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/40 border border-red-300 dark:border-red-700
                    text-red-800 dark:text-red-200 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800
                shadow-xl rounded-2xl p-8 space-y-6">

        {{-- Info propiedad / visita / comprador --}}
        <div class="space-y-1 text-sm">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $property->title }}
            </h2>

            @if(!empty($client))
                <p>
                    <strong>Comprador:</strong>
                    {{ $client->name }}
                    <span class="text-gray-400">({{ $client->email }})</span>
                </p>
            @else
                <p class="text-gray-400">
                    Comprador no especificado en la venta.
                </p>
            @endif

            @if(!empty($visit) && $visit->visit_date)
                <p>
                    <strong>Fecha de visita:</strong>
                    {{ $visit->visit_date->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>

        <hr class="border-gray-300 dark:border-gray-700 my-6">

        {{-- Detalles de la venta --}}
        <div class="space-y-3 text-sm">
            <div>
                <span class="font-semibold">Precio de venta (MXN): </span>
                ${{ number_format($sale->sale_price, 2) }}
            </div>

            <div>
                <span class="font-semibold">Comisión aplicada: </span>
                @php
                    $percent = $commission?->percentage ?? null;
                @endphp
                {{ $percent ? $percent . '%' : 'Sin configuración de comisión' }}
            </div>

            <div>
                <span class="font-semibold">Comisión a pagar: </span>
                <span class="text-blue-400 font-bold">
                    ${{ number_format($sale->commission_amount, 2) }} MXN
                </span>
            </div>
        </div>

        {{-- BLOQUE DE PAGO --}}
        @if(is_null($sale->commission_paid_at))
            <div class="mt-6 space-y-3">
                <h3 class="font-semibold text-base">Pagar comisión</h3>
                <div id="paypal-button-container"></div>
                <p id="paypal-error"
                   class="hidden mt-2 text-sm text-red-500">
                    No se pudo cargar el botón de PayPal. Revisa tu Client ID o vuelve a intentar.
                </p>
            </div>
        @else
            <p class="mt-4 text-green-500 font-semibold">
                La comisión ya fue pagada para esta venta.
            </p>
        @endif

    </div>
</main>

<x-main-footer />

{{-- SDK + lógica PayPal (solo si NO está pagada) --}}
@if(is_null($sale->commission_paid_at))
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency={{ config('services.paypal.currency', 'MXN') }}&intent=capture&components=buttons"></script>

    <script>
        (function () {
            const containerId = '#paypal-button-container';
            const errorEl = document.getElementById('paypal-error');

            if (typeof paypal === 'undefined') {
                if (errorEl) errorEl.classList.remove('hidden');
                return;
            }

            paypal.Buttons({
                createOrder: function () {
                    return fetch("{{ route('agent.paypal.commission.create', $visit->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            sale_id: "{{ $sale->id }}"
                        })
                    })
                        .then(res => res.json())
                        .then(function (data) {
                            if (data && data.id) {
                                return data.id;
                            }
                            throw new Error(data?.error || 'No se pudo crear la orden de PayPal.');
                        });
                },

                onApprove: function (data) {
                    return fetch("{{ route('agent.paypal.captureCommission', $visit->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            order_id: data.orderID,
                            sale_id: "{{ $sale->id }}"
                        })
                    })
                        .then(res => res.json())
                        .then(function (response) {
                            if (response.redirect) {
                                return window.location.href = response.redirect;
                            }
                            if (response.success) {
                                alert("Pago registrado correctamente.");
                                return window.location.reload();
                            }
                            alert(response.error || "Error en el registro del pago.");
                        })
                        .catch(function () {
                            alert("Error al comunicar con el servidor");
                        });
                }
            }).render(containerId);
        })();
    </script>
@endif

</body>
</html>
