<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar venta - SIN BECA NO HAY RENTA</title>

    <!-- Anti-flash -->
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>

    <!-- Iconos -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">

    <!-- HEADER -->
    <x-main-header />

    <!-- BOTÓN TEMA -->
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
               bg-white text-gray-800 hover:bg-gray-100
               dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 transition">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    <!-- CONTENIDO -->
    <main class="flex-grow max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-3xl font-bold mb-8 flex items-center gap-3 text-gray-900 dark:text-gray-100">
            <i class="fa-solid fa-receipt text-blue-500"></i>
            Registrar venta de propiedad
        </h1>

        <!-- MENSAJE DE ÉXITO -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/40 border border-green-300 dark:border-green-700
                        text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- CARD -->
        <div class="bg-white dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800
                    shadow-xl rounded-2xl p-8 space-y-6">

            <!-- INFO PROPIEDAD -->
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $property->title }}
            </h2>

            <p><strong>Cliente:</strong> {{ $client->name }}</p>
            <p><strong>Fecha de visita:</strong> {{ $visit->visit_date->format('d/m/Y H:i') }}</p>

            <hr class="border-gray-300 dark:border-gray-700 my-6">

            @if(!$sale)
            <!-- FORMULARIO REGISTRO VENTA -->
            <form method="POST" action="{{ route('agent.visits.sale.store', $visit) }}" class="space-y-6">
                @csrf

                <!-- PRECIO -->
                <div>
                    <label class="block font-medium mb-1">Precio de venta (MXN)</label>
                    <input type="number" step="0.01"
                           name="sale_price"
                           value="{{ old('sale_price', $property->price ?? '') }}"
                           required
                           class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                                  px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- COMISIÓN -->
                <div>
                    <label class="block font-medium mb-1">Comisión aplicada</label>
                    <input type="text" disabled
                           value="{{ $commission ? $commission->percentage.'%' : '0%' }}"
                           class="w-full rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                                  px-3 py-2 text-gray-600 dark:text-gray-300 cursor-not-allowed">
                </div>

                <!-- NOTAS -->
                <div>
                    <label class="block font-medium mb-1">Notas (opcional)</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                                     px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>

                <button
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow transition">
                    Registrar venta
                </button>
            </form>

            @else

            <!-- SI YA EXISTE VENTA -->
            <h2 class="text-xl font-semibold mb-2">Pagar comisión</h2>

            <p>
                Comisión a pagar:
                <strong>${{ number_format($sale->commission_amount, 2) }} MXN</strong>
            </p>

            @if (is_null($sale->commission_paid_at))
                <div id="paypal-button-container" class="mt-6"></div>
            @else
                <p class="text-green-600 font-semibold mt-4">Comisión pagada correctamente.</p>
            @endif

            <!-- PAYPAL -->
            <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}
                &currency={{ config('services.paypal.currency', 'MXN') }}
                &intent=capture
                &components=buttons"></script>

            <script>
            paypal.Buttons({
                createOrder: function() {
                    return fetch("{{ route('agent.paypal.commission.create', $visit->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(function (data) {
                        if (data?.id) return data.id;
                        throw new Error(data?.error || 'No se pudo crear la orden de PayPal.');
                    });
                },

                onApprove: function(data) {
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
                    .then(function(response) {
                        if (response.redirect) {
                            return window.location.href = response.redirect;
                        }
                        if (response.success) {
                            alert("Pago registrado correctamente.");
                            return window.location.reload();
                        }
                        alert(response.error || "Error en el registro del pago.");
                    })
                    .catch(() => alert("Error al comunicar con el servidor"));
                }

            }).render('#paypal-button-container');
            </script>

            @endif
        </div>
    </main>

    <!-- FOOTER -->
    <x-main-footer />

    <!-- SCRIPT MODO OSCURO -->
    <script>
        (function () {
            const html  = document.documentElement;
            const btn   = document.getElementById('theme-toggle');
            const icon  = document.getElementById('theme-toggle-icon');
            const label = btn.querySelector('span');

            function syncUI() {
                const isDark = html.classList.contains('dark');
                icon.classList.remove('fa-sun', 'fa-moon');
                icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
                label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
            }

            syncUI();

            btn.addEventListener('click', () => {
                const isDark = !html.classList.contains('dark');
                html.classList.toggle('dark', isDark);
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                syncUI();
            });
        })();
    </script>

</body>
</html>
