<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta - SIN BECA NO HAY RENTA</title>

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

    {{-- Header con logo centrado --}}
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
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-3xl px-6 sm:px-8 py-7 sm:py-8">

                {{-- Encabezado dentro de la tarjeta (sin bullets) --}}
                <div class="mb-6">
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/40 mb-3">
                        <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <h1 class="text-2xl font-bold mb-1">
                        Crea tu cuenta
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                        Regístrate para guardar favoritos, gestionar reservas y comunicarte directamente con agentes.
                    </p>
                </div>

                {{-- FORMULARIO --}}
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Nombre completo
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                   px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Teléfono
                        </label>
                        <input
                            id="phone"
                            type="number"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                   px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

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
                            autocomplete="username"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                   px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label for="bio" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Bio
                        </label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="3"
                            class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                   px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Avatar --}}
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Avatar (opcional)
                        </label>
                        <input
                            id="avatar"
                            type="file"
                            name="avatar"
                            accept="image/*"
                            class="mt-1 block w-full text-sm
                                   text-slate-900 dark:text-slate-100
                                   border border-slate-300 dark:border-slate-700
                                   rounded-lg bg-white dark:bg-slate-900
                                   file:mr-3 file:py-2 file:px-4
                                   file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-600 file:text-white
                                   hover:file:bg-blue-700
                                   file:cursor-pointer"
                        >
                        @error('avatar')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Passwords --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Contraseña
                            </label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                       px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Confirmar contraseña
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900
                                       px-3 py-2 text-sm text-slate-900 dark:text-slate-100
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Link + botón --}}
                    <div class="pt-2 flex items-center justify-between">
                        <a href="{{ route('login') }}"
                           class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 underline">
                            ¿Ya tienes cuenta? Inicia sesión
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-slate-100 dark:focus:ring-offset-slate-950"
                        >
                            <i class="fa-solid fa-user-check mr-2 text-xs"></i>
                            Registrarme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>
