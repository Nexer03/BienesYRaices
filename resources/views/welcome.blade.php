<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- HEADER -->
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                Sin beca<span class="text-gray-700">no hay </span>
            </div>
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                <a href="#" class="hover:text-blue-600 transition">Propiedades</a>
                <a href="#" class="hover:text-blue-600 transition">Mapa</a>
                <a href="#" class="hover:text-blue-600 transition">Modo vendedor</a>
                <button id="loginTrigger"
                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
                    Iniciar sesión
                </button>
            </nav>
        </div>
    </header>

    <!-- FILTRO / BUSCADOR -->
    <section class="bg-white shadow-sm w-full py-4">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center gap-3 px-6">
            <!-- Placeholder de filtro -->
            <input type="text" placeholder="¿Dónde buscas?"
                class="w-full md:w-1/3 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="date"
                class="w-full md:w-1/4 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="date"
                class="w-full md:w-1/4 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="w-full md:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                Buscar
            </button>
        </div>
    </section>

    <!-- CUERPO -->
    <main class="max-w-7xl mx-auto mt-10 px-6 space-y-12">

        <!-- TEMPLATE DE CARRUSEL -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades en Ciudad 1</h2>
            <div class="relative">
                <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4">
                    <!-- CARD DE PROPIEDAD -->
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="min-w-[250px] bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer"
                            onclick="openLoginModal()">
                            <img src="https://via.placeholder.com/300x200"
                                alt="Propiedad {{ $i }}"
                                class="w-full h-48 object-cover rounded-t-xl">
                            <div class="p-3">
                                <h3 class="font-semibold text-lg">Casa moderna #{{ $i }}</h3>
                                <p class="text-sm text-gray-500">Ciudad Ejemplo</p>
                                <p class="mt-1 font-semibold">Precio $$$$</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- MÁS CARRUSELES (placeholder) -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Propiedades en Ciudad 2</h2>
            <div class="relative">
                <div class="flex space-x-4 overflow-x-auto scrollbar-hide pb-4">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="min-w-[250px] bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer"
                            onclick="openLoginModal()">
                            <img src="https://via.placeholder.com/300x200"
                                alt="Propiedad {{ $i }}"
                                class="w-full h-48 object-cover rounded-t-xl">
                            <div class="p-3">
                                <h3 class="font-semibold text-lg">Casa moderna #{{ $i }}</h3>
                                <p class="text-sm text-gray-500">Ciudad Ejemplo</p>
                                <p class="mt-1 font-semibold">Precio $$$$</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-100 mt-20 w-full py-10 border-t">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 px-6 text-sm text-gray-600">
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Soporte</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Centro de ayuda</a></li>
                    <li><a href="#" class="hover:text-blue-600">Preguntas frecuentes</a></li>
                    <li><a href="#" class="hover:text-blue-600">Reportar problema</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Compañía</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Sobre nosotros</a></li>
                    <li><a href="#" class="hover:text-blue-600">Carreras</a></li>
                    <li><a href="#" class="hover:text-blue-600">Blog</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Legal</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Privacidad</a></li>
                    <li><a href="#" class="hover:text-blue-600">Términos</a></li>
                    <li><a href="#" class="hover:text-blue-600">Cookies</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800 mb-2">Síguenos</h3>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Instagram</a></li>
                    <li><a href="#" class="hover:text-blue-600">Facebook</a></li>
                    <li><a href="#" class="hover:text-blue-600">Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-gray-500 mt-10 text-sm">
            hey todos estos campos son genericos y no representan la version final, diganme cual esta pana y cual no
        </div>
    </footer>

    <!-- MODAL DE LOGIN -->
    <div id="loginModal"
        class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-11/12 max-w-sm shadow-lg relative">
            <button onclick="closeLoginModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                ✕
            </button>
            <h2 class="text-2xl font-semibold mb-4 text-center">Iniciar sesión</h2>
            <input type="email" placeholder="Correo electrónico"
                class="w-full border rounded-lg px-4 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="password" placeholder="Contraseña"
                class="w-full border rounded-lg px-4 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                Entrar
            </button>
            <p class="text-center text-sm text-gray-600 mt-4">
                ¿No tienes cuenta? <a href="#" class="text-blue-500 hover:underline">Regístrate</a>
            </p>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        const modal = document.getElementById('loginModal');
        const trigger = document.getElementById('loginTrigger');

        function openLoginModal() {
            modal.classList.remove('hidden');
        }

        function closeLoginModal() {
            modal.classList.add('hidden');
        }

        trigger.addEventListener('click', openLoginModal);
        modal.addEventListener('click', e => {
            if (e.target === modal) closeLoginModal();
        });
    </script>
</body>
</html>
