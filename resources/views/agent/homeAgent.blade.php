<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Agente - SIN BECA NO HAY RENTA</title>

    <!-- Anti-flash: por defecto CLARO; si guardaste 'dark', lo aplica -->
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark'); // claro por defecto
                }
            } catch (e) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>

    <!-- Iconos -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-300">

    <!-- Header -->
    <x-main-header />

    <!-- Botón Tema (flotante) -->
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
               bg-white text-gray-800 hover:bg-gray-100
               dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700
               transition-colors duration-200"
        aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    <!-- Contenido principal -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Sección de bienvenida -->
        <section class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                Bienvenido a la vista de agente
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Gestiona tus propiedades, agendas y comunicaciones desde un solo lugar
            </p>
        </section>

        <!-- Botón Crear Nueva Propiedad -->
        <div class="text-center mb-12">
            <a href="{{ route('properties.create') }}"
               class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white
                      font-semibold rounded-lg px-6 py-3 transition-colors duration-200
                      shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Crear Nueva Propiedad</span>
            </a>
        </div>

               <!-- Grid de acciones -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6 mb-12">

            <!-- Mis Propiedades -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center lg:col-span-2">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Mis Propiedades
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Gestiona y visualiza todas tus propiedades listadas
                </p>
                <a href="{{ route('properties.my') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver propiedades →
                </a>
            </div>

            <!-- Agendas -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center lg:col-span-2">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Agendas
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Administra tus citas y visitas programadas
                </p>
                <a href="{{ route('agent.visits.index') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver agenda →
                </a>
            </div>

            <!-- Mensajería -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center lg:col-span-2">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Mensajería
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Comunícate con clientes y prospectos
                </p>
                <a href="{{ route('chat.index') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Abrir mensajería →
                </a>
            </div>

            <!-- Mis sugerencias (fila de abajo, centrada) -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center lg:col-span-2 lg:col-start-2">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Mis sugerencias
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Revisa y administra las sugerencias que has enviado
                </p>
                <a href="{{ route('agent.suggestions.index') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duración-200">
                    Ver sugerencias →
                </a>
            </div>

            <!-- Estadísticas (fila de abajo, centrada) -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center lg:col-span-2 lg:col-start-4">
                <div class="text-blue-500 text-3xl mb-4 relative inline-block">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Estadísticas
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    Resumen de propiedades y visitas en el tiempo
                </p>
                <a href="{{ route('agent.analytics') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver estadísticas →
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <x-main-footer />

    <!-- Script modo oscuro (simple y seguro) -->
    <script>
        (function () {
            const html  = document.documentElement;
            const btn   = document.getElementById('theme-toggle');
            const icon  = document.getElementById('theme-toggle-icon');
            const label = btn ? btn.querySelector('span') : null;

            function syncUI() {
                const isDark = html.classList.contains('dark');
                if (icon) {
                    icon.classList.remove('fa-sun', 'fa-moon');
                    icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
                }
                if (label) {
                    label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
                }
            }

            // Estado inicial: según localStorage
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            } catch (e) {}

            syncUI();

            if (btn) {
                btn.addEventListener('click', () => {
                    const isDark = !html.classList.contains('dark');
                    html.classList.toggle('dark', isDark);
                    try {
                        localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    } catch (e) {}
                    syncUI();
                });
            }
        })();
    </script>
</body>
</html>
