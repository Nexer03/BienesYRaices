<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva visita - SIN BECA NO HAY RENTA</title>

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
    <script> tailwind.config = { darkMode: 'class' }; </script>

    <!-- Iconos -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-300">

    <!-- HEADER -->
    <x-main-header />

    <!-- MAIN -->
    <main class="flex-grow max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">

        <!-- TÍTULO -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8 flex items-center gap-3">
            <i class="fa-solid fa-calendar-plus text-blue-500"></i>
            Nueva visita
        </h1>

        <!-- ERRORES -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/40 border border-red-300 dark:border-red-700
                        text-red-700 dark:text-red-200 px-4 py-3 rounded-xl">
                <ul class="space-y-1 list-disc ml-5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- CARD FORMULARIO -->
        <form method="POST" action="{{ route('agent.visits.store') }}"
              class="bg-white dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800
                     shadow-xl rounded-2xl p-8 space-y-6 backdrop-blur-md">

            @csrf

            <!-- PROPIEDAD -->
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Propiedad</label>
                <select name="property_id" required
                        class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                               px-3 py-2 text-gray-800 dark:text-gray-200
                               focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Selecciona una propiedad</option>

                    @foreach($properties as $id => $title)
                        <option value="{{ $id }}" @selected(old('property_id')==$id)>
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
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Cliente</label>
                <select name="client_id" required
                        class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                               px-3 py-2 text-gray-800 dark:text-gray-200
                               focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Selecciona un cliente</option>

                    @foreach($clients as $id => $name)
                        <option value="{{ $id }}" @selected(old('client_id')==$id)>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>

                @error('client_id')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- FECHA Y HORA -->
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Fecha y hora</label>
                <input type="datetime-local"
                       name="visit_date"
                       value="{{ old('visit_date') }}"
                       required
                       class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                              px-3 py-2 text-gray-800 dark:text-gray-200
                              focus:ring-blue-500 focus:border-blue-500 outline-none">

                @error('visit_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- NOTAS -->
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Notas</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                                 px-3 py-2 text-gray-800 dark:text-gray-200
                                 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('notes') }}</textarea>

                @error('notes')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ url()->previous() }}"
                   class="px-5 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                          text-gray-700 dark:text-gray-300
                          hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    Cancelar
                </a>

                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow
                               transition">
                    Guardar
                </button>
            </div>

        </form>

    </main>

    <!-- FOOTER -->
    <x-main-footer />

</body>
</html>
