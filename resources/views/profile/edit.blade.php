<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - SIN BECA NO HAY RENTA</title>

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
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">

    {{-- HEADER GLOBAL --}}
    <x-main-header />

    @php
        $user = auth()->user();
    @endphp

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- HERO PERFIL --}}
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-sky-500 to-emerald-400 text-white shadow-xl">
                <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top,_#ffffff,_transparent_55%)]"></div>

                <div class="relative px-6 py-7 sm:px-8 sm:py-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        {{-- Avatar simple con inicial --}}
                        <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-full bg-white/15 border border-white/40 flex items-center justify-center text-2xl font-bold">
                            {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-semibold">
                                {{ $user->name ?? 'Mi perfil' }}
                            </h1>
                            <p class="text-sm sm:text-base text-white/90 flex items-center gap-2">
                                <i class="fa-regular fa-envelope text-xs"></i>
                                <span>{{ $user->email ?? 'Correo no disponible' }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- ÚNICO CHIP: MIEMBRO DESDE --}}
                    <div class="grid grid-cols-1 gap-4 text-sm sm:text-xs md:text-sm max-w-xs w-full">
                        <div class="px-3 py-2 rounded-xl bg-white/10 border border-white/20">
                            <p class="uppercase tracking-wide text-[11px] font-semibold text-white/70">
                                Miembro desde
                            </p>
                            <p class="font-medium">
                                {{ optional($user->created_at)->format('d/m/Y') ?? 'N/D' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- LAYOUT DOS COLUMNAS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- COLUMNA IZQUIERDA: RESUMEN / TIPS --}}
                <aside class="space-y-4 lg:space-y-6">
                    <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-5">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500 text-xs"></i>
                            Resumen de la cuenta
                        </h2>
                        <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-200">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span>Actualiza tus datos de perfil para que los agentes y clientes te identifiquen fácilmente.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                <span>Usa una contraseña segura y no la compartas con nadie.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                <span>La eliminación de cuenta es permanente, úsala solo si estás seguro.</span>
                            </li>
                        </ul>
                    </section>

                    <section class="bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-slate-900 dark:to-slate-900/70 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 text-sm">
                        <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-lightbulb text-amber-400"></i>
                            Sugerencia
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300">
                            Mantén tu información actualizada para mejorar la experiencia en recomendaciones,
                            contacto y procesos dentro de <span class="font-semibold">SIN BECA NO HAY RENTA</span>.
                        </p>
                    </section>
                </aside>

                {{-- COLUMNA DERECHA: FORMULARIOS --}}
                <section class="lg:col-span-2 space-y-6">

                    {{-- Datos de perfil --}}
                    <div class="p-4 sm:p-6 bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="h-9 w-9 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-id-card text-blue-600 dark:text-blue-300 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    Información de perfil
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    Nombre, correo y otros datos básicos de tu cuenta.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div class="p-4 sm:p-6 bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="h-9 w-9 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-lock text-amber-500 dark:text-amber-300 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    Seguridad y contraseña
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    Cambia tu contraseña regularmente para mantener tu cuenta protegida.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Eliminar cuenta --}}
                    <div class="p-4 sm:p-6 bg-white dark:bg-slate-900 shadow-sm border border-rose-200 dark:border-rose-800/70 rounded-2xl">
                        <div class="mb-4 flex items-center gap-2">
                            <div class="h-9 w-9 rounded-full bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-300 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-semibold text-rose-700 dark:text-rose-300">
                                    Eliminar cuenta
                                </h2>
                                <p class="text-xs sm:text-sm text-rose-500 dark:text-rose-300/80">
                                    Esta acción es permanente. Se borrarán tus datos y no podrás recuperarlos.
                                </p>
                            </div>
                        </div>

                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </main>

    {{-- FOOTER GLOBAL --}}
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
