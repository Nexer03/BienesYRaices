<!-- resources/views/welcome.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col items-center justify-center min-h-screen">

    <h1 class="text-4xl font-bold mb-6">Bienvenido a Bienes Raíces</h1>

    @auth
        <!-- Usuario logueado -->
        <p class="mb-4 text-lg">Hola, {{ auth()->user()->name }}!</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                Cerrar sesión
            </button>
        </form>
    @endauth

    @guest
        <!-- Usuario no logueado -->
        <div class="flex space-x-4">
            <a href="{{ route('login') }}"
               class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
               Iniciar sesión
            </a>
            <a href="{{ route('register') }}"
               class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
               Registrarse
            </a>
        </div>
    @endguest

</body>
</html>
