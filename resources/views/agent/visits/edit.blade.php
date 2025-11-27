<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar visita - SIN BECA NO HAY RENTA</title>

    <!-- Anti-flash: mantener tema -->
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

<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">

    <!-- HEADER GLOBAL -->
    <x-main-header />

    <!-- Botón flotante para tema -->
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
            <i class="fa-solid fa-pen-to-square text-blue-500"></i>
            Editar visita
        </h1>

        <!-- ERRORES -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/40 border border-red-300 dark:border-red-700
                        text-red-700 dark:text-red-200 px-4 py-3 rounded-xl">
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- CARD -->
        <form method="POST" action="{{ route('agent.visits.update', $visit) }}"
              class="bg-white dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800
                     shadow-xl rounded-2xl p-8 space-y-6">

            @csrf
            @method('PUT')

            <!-- PROPIEDAD -->
            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Propiedad</label>
                <select name="property_id" required
                    class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                           px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">

                    @foreach($properties as $id => $title)
                        <option value="{{ $id }}" @selected(old('property_id', $visit->property_id) == $id)>
                            {{ $title }}
                        </option>
                    @endforeach

                </select>
                @error('property_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- CLIENTE -->
            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Cliente</label>
                <select name="client_id" required
                    class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                           px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">

                    @foreach($clients as $id => $name)
                        <option value="{{ $id }}" @selected(old('client_id', $visit->client_id) == $id)>
                            {{ $name }}
                        </option>
                    @endforeach

                </select>
                @error('client_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- FECHA -->
            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha y hora</label>
                <input type="datetime-local"
                       name="visit_date"
                       value="{{ old('visit_date', optional($visit->visit_date)->format('Y-m-d\TH:i')) }}"
                       required
                       class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                              px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                @error('visit_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ESTADO -->
            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>

                @php
                    $estados = [
                        'pending'   => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ];
                @endphp

                <select name="status" required
                    class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                           px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">

                    @foreach($estados as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $visit->status) === $key)>
                            {{ $label }}
                        </option>
                    @endforeach

                </select>

                @error('status')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- NOTAS -->
            <div>
                <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Notas</label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                           px-3 py-2 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $visit->notes) }}</textarea>

                @error('notes')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- BOTONES -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">

                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow transition">
                    Guardar cambios
                </button>

                <a href="{{ route('agent.visits.index') }}"
                    class="px-5 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                           text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    Cancelar
                </a>

                @if ($visit->status === 'completed')
                    <a href="{{ route('agent.visits.sale', $visit) }}"
                       class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow transition">
                        Registrar venta de la propiedad
                    </a>
                @endif

            </div>

        </form>

    </main>

    <!-- FOOTER -->
    <x-main-footer />

    <!-- Script modo oscuro -->
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
