<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - SIN BECA NO HAY RENTA</title>

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
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    {{-- Iconos --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Fade suave al cambiar tema */
        html.theme-fade * {
            transition:
                background-color .35s ease,
                color .35s ease,
                border-color .35s ease,
                fill .35s ease;
        }

        /* Animación del botón de tema */
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
<body class="bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 flex flex-col min-h-screen">

    {{-- Header principal --}}
    <x-main-header />

    {{-- Contenido principal --}}
    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                Mensajes
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Consulta tus conversaciones con agentes y clientes.
            </p>
        </header>

        @if ($conversations->isEmpty())
            <div class="mt-6 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-6 py-8 text-center">
                <div class="mb-2 text-gray-500 dark:text-gray-400">
                    <i class="fa-regular fa-comments text-3xl mb-2"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    No tienes conversaciones todavía.
                </p>
            </div>
        @else
            <section class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($conversations as $c)
                        @php
                            $isAgent = (auth()->id() === $c->agent_id);
                            $other   = $isAgent ? $c->client : $c->agent;
                        @endphp

                        <li>
                            <a href="{{ route('chat.open', $c) }}"
                               class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $other->name ?? 'Usuario' }}
                                        </p>
                                        @if($c->property)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 dark:bg-blue-900/40 px-2 py-0.5 text-[11px] font-medium text-blue-700 dark:text-blue-300">
                                                <i class="fa-solid fa-house-chimney text-[10px]"></i>
                                                {{ Str::limit($c->property->title, 40) }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $isAgent ? 'Eres el agente' : 'Eres el cliente' }}
                                    </p>
                                </div>

                                @if(($c->unread_count ?? 0) > 0)
                                    <span class="shrink-0 inline-flex items-center justify-center rounded-full
                                                 bg-blue-600 text-white text-xs font-semibold px-2 py-1">
                                        {{ $c->unread_count }} nuevo{{ $c->unread_count > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $conversations->links() }}
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <x-main-footer />

    {{-- Botón Tema (mismo que en la home) --}}
    <button id="theme-toggle"
            class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                   bg-white text-gray-800 hover:bg-gray-100
                   dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
            aria-label="Cambiar tema">
        <i id="theme-toggle-icon" class="fa-solid"></i>
        <span class="text-sm font-medium"></span>
    </button>

    {{-- Script del botón de tema --}}
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
                try {
                    localStorage.setItem('theme', mode);
                } catch (e) {}
                setIconAndLabel();
                animateButton();
            }

            // Estado inicial
            setIconAndLabel();

            btn?.addEventListener('click', () => {
                const next = html.classList.contains('dark') ? 'light' : 'dark';
                apply(next);
            });
        })();
    </script>
</body>
</html>
