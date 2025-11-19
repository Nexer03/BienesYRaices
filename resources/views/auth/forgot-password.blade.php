<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - SIN BECA NO HAY RENTA</title>

    {{-- Anti-flash --}}
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
        tailwind.config = { darkMode: 'class' };
    </script>

    {{-- Iconos --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 via-slate-100 to-slate-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-slate-900 dark:text-slate-100 flex flex-col">

    {{-- Logo centrado --}}
    <header class="w-full px-6 pt-8 flex justify-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-800 dark:text-slate-100">
            <i class="fa-solid fa-house-chimney text-blue-500"></i>
            <span class="font-semibold tracking-tight">
                <span class="text-blue-600">Sin beca</span> no hay renta
            </span>
        </a>
    </header>

    {{-- CONTENIDO PRINCIPAL --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-3xl px-6 sm:px-8 py-7 sm:py-8">

                {{-- Encabezado --}}
                <div class="mb-5">
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/40 mb-3">
                        <i class="fa-regular fa-envelope text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <h1 class="text-2xl font-bold mb-1">
                        ¿Olvidaste tu contraseña?
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        No hay problema. Escríbenos el correo con el que te registraste y te enviaremos un enlace para que puedas crear una nueva contraseña.
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 text-xs sm:text-sm rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 px-3 py-2">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                   px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 space-y-3">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-slate-100 dark:focus:ring-offset-slate-950"
                        >
                            <i class="fa-solid fa-paper-plane mr-2 text-xs"></i>
                            Enviarme enlace de recuperación
                        </button>

                        <p class="text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                            ¿Ya recordaste tu contraseña?
                            <a href="{{ route('login') }}"
                               class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                Volver a iniciar sesión
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>
