<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - SIN BECA NO HAY RENTA</title>

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

    {{-- Logo --}}
    <header class="w-full px-6 pt-8 flex justify-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-800 dark:text-slate-100">
            <i class="fa-solid fa-house-chimney text-blue-500"></i>
            <span class="font-semibold tracking-tight">
                <span class="text-blue-600">Sin beca</span> no hay renta
            </span>
        </a>
    </header>

    {{-- Contenido --}}
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-3xl px-6 sm:px-8 py-7 sm:py-8">

                {{-- Icono + título --}}
                <div class="mb-5">
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/40 mb-3">
                        <i class="fa-solid fa-lock text-blue-600 dark:text-blue-300"></i>
                    </div>

                    <h1 class="text-2xl font-bold mb-1">
                        Restablecer contraseña
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        Ingresa tu nueva contraseña y confírmala para recuperar acceso a tu cuenta.
                    </p>
                </div>

                {{-- Formulario --}}
                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf

                    {{-- TOKEN --}}
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $request->email) }}"
                            required
                            autocomplete="username"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700
                                   bg-white dark:bg-slate-900 px-3 py-2 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium">Nueva contraseña</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700
                                   bg-white dark:bg-slate-900 px-3 py-2 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmación --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium">Confirmar contraseña</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700
                                   bg-white dark:bg-slate-900 px-3 py-2 text-sm
                                   text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('password_confirmation')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botón --}}
                    <div class="pt-3">
                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-slate-100 dark:focus:ring-offset-slate-950"
                        >
                            <i class="fa-solid fa-rotate-right mr-2 text-xs"></i>
                            Restablecer contraseña
                        </button>
                    </div>

                    {{-- Volver login --}}
                    <p class="text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-3">
                        ¿Ya recordaste tu contraseña?
                        <a href="{{ route('login') }}"
                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">
                            Volver a iniciar sesión
                        </a>
                    </p>

                </form>
            </div>
        </div>
    </main>

</body>
</html>
