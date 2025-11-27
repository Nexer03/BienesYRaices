<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerencias de clientes - SIN BECA NO HAY RENTA</title>

    {{-- Anti-flash: por defecto CLARO; si guardaste "dark", lo aplica ANTES de pintar la página --}}
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

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>

    {{-- Iconos --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Fade suave al cambiar de tema (se activa solo mientras se anima) */
        .theme-transition,
        .theme-transition * {
            transition:
                background-color .35s ease,
                color .35s ease,
                border-color .35s ease,
                fill .35s ease;
        }

        /* Animación del botón de tema (rebote + sombra) */
        #theme-toggle {
            transition:
                background-color .25s ease,
                color .25s ease,
                transform .25s ease,
                box-shadow .25s ease;
        }

        #theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.55);
        }

        #theme-toggle.theme-bounce {
            transform: translateY(-1px) scale(1.04);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.75);
        }

        #theme-toggle-icon {
            transition: transform .35s ease, opacity .2s ease;
        }

        #theme-toggle-icon.theme-spin {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-300">

    {{-- Header global --}}
    <x-main-header />

    {{-- Botón Tema (flotante, mismo patrón que la vista de agente) --}}
    <button id="theme-toggle"
        class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
               bg-white text-gray-800 hover:bg-gray-100
               dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700
               transition-colors duration-200"
        aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    {{-- CONTENIDO --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Encabezado --}}
        <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold tracking-wide text-blue-600 uppercase dark:text-blue-400">
                    Panel de agente
                </p>
                <h1 class="mt-1 text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    <span>Sugerencias de tus clientes</span>
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 max-w-xl">
                    Revisa comentarios privados que tus clientes dejan después de sus visitas y rentas
                    para mejorar tu atención y la presentación de tus propiedades.
                </p>
            </div>

            {{-- Filtro por origen --}}
            <form method="GET"
                  class="flex items-center gap-3 bg-white/80 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-700 rounded-2xl px-4 py-3 shadow-sm">
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                        Filtrar por origen
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Visitas o rentas</span>
                </div>
                <select id="type"
                        name="type"
                        onchange="this.form.submit()"
                        class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm text-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                    <option value="">Todas</option>
                    <option value="reservation" @selected($type === 'reservation')>Rentas</option>
                    <option value="visit" @selected($type === 'visit')>Visitas</option>
                </select>
            </form>
        </header>

        {{-- Tarjeta principal --}}
        <section class="bg-white/90 dark:bg-gray-900/80 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">

            {{-- Barra superior --}}
            <div class="px-6 md:px-8 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-2xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-100">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div class="space-y-0.5">
                        <p class="font-semibold text-gray-800 dark:text-gray-100">
                            Notificaciones privadas de clientes
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Solo tú puedes ver estas sugerencias. No se comparten con otros agentes ni con el cliente.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total de sugerencias</span>
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-100 font-semibold">
                        {{ $suggestions->total() }}
                    </span>
                </div>
            </div>

            {{-- Lista de sugerencias --}}
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($suggestions as $suggestion)
                    <article class="px-6 md:px-8 py-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between hover:bg-gray-50/80 dark:hover:bg-gray-900/70 transition-colors">
                        <div class="space-y-2">
                            {{-- Badges de tipo / tiempo / referencia --}}
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold tracking-wide uppercase
                                    {{ $suggestion->type === 'visit'
                                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-100'
                                        : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-100' }}">
                                    {{ $suggestion->type === 'visit' ? 'Visita' : 'Renta' }}
                                </span>

                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $suggestion->created_at?->diffForHumans() }}
                                </span>

                                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="fa-solid fa-hashtag"></i>
                                    {{ $suggestion->type === 'visit'
                                        ? 'Visita #' . $suggestion->visit_id
                                        : 'Reserva #' . $suggestion->reservation_id }}
                                </span>
                            </div>

                            {{-- Propiedad --}}
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-50 flex items-center gap-2">
                                <i class="fa-solid fa-house-chimney text-blue-500"></i>
                                <span>{{ $suggestion->property?->title ?? 'Propiedad' }}</span>
                            </h2>

                            {{-- Cliente --}}
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium text-gray-800 dark:text-gray-100">Cliente:</span>
                                {{ $suggestion->user?->name ?? '—' }}
                            </p>

                            {{-- Mensaje --}}
                            <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                                {{ $suggestion->message }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="px-6 md:px-8 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 mb-3">
                            <i class="fa-regular fa-comment-dots text-lg"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            Aún no tienes sugerencias registradas.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md mx-auto">
                            Cuando tus clientes dejen comentarios después de una visita o una renta,
                            aparecerán aquí para que puedas revisarlos y mejorar tu servicio.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="px-6 md:px-8 py-4 bg-gray-50 dark:bg-gray-900/70 border-t border-gray-200 dark:border-gray-800">
                {{ $suggestions->links() }}
            </div>
        </section>
    </main>

    {{-- Footer global --}}
    <x-main-footer />

    {{-- Script modo oscuro con animación de botón y fade anti-flash en el cambio --}}
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

            function animateButton() {
                if (!btn || !icon) return;
                btn.classList.add('theme-bounce');
                icon.classList.add('theme-spin');
                setTimeout(() => {
                    btn.classList.remove('theme-bounce');
                    icon.classList.remove('theme-spin');
                }, 350);
            }

            function startFade() {
                html.classList.add('theme-transition');
                setTimeout(() => {
                    html.classList.remove('theme-transition');
                }, 400);
            }

            function apply(isDark) {
                // fade suave para que no "encandile"
                startFade();

                html.classList.toggle('dark', isDark);
                try {
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                } catch (e) {}

                syncUI();
                animateButton();
            }

            // Estado inicial (ya viene de anti-flash del <head>, solo sincronizamos icono/texto)
            syncUI();

            if (btn) {
                btn.addEventListener('click', () => {
                    const nextIsDark = !html.classList.contains('dark');
                    apply(nextIsDark);
                });
            }
        })();
    </script>
</body>
</html>
