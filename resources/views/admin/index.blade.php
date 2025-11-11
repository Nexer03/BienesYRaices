<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel Administrador - SIN BECA NO HAY RENTA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
  <x-main-header />

  <!-- Contenido principal -->
  <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Encabezado -->
    <section class="text-center mb-12">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
        Panel de Administrador
      </h1>
      <p class="text-lg text-gray-600 max-w-2xl mx-auto">
        Administra propiedades, usuarios, solicitudes y reportes desde un solo lugar.
      </p>
    </section>

    <!-- Acciones clave arriba -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <!-- Gestionar Propiedades -->
      <a href="{{ route('admin.properties.index') }}"
         class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl"><i class="fas fa-building"></i></div>
          <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-1 rounded-full">Core</span>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Gestionar Propiedades</h3>
        <p class="text-gray-600">Ver, filtrar y eliminar propiedades del sistema.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>

      <!-- Gestionar Usuarios -->
      <a href="{{ route('admin.users.index') }}"
         class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl"><i class="fas fa-users-cog"></i></div>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Gestionar Usuarios</h3>
        <p class="text-gray-600">Ver, editar, filtrar y eliminar usuarios.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>

      <!-- Solicitudes de Agentes -->
      <a href="{{ route('admin.agent-applications.index') }}"
         class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl"><i class="fas fa-id-card-check"></i></div>
          <span class="text-[10px] bg-amber-100 text-amber-700 font-semibold px-2 py-0.5 rounded-full uppercase">Revisión</span>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Solicitudes de Agentes</h3>
        <p class="text-gray-600">Revisar, aprobar o rechazar solicitudes.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>
    </div>

    <!-- Reportes y Finanzas -->
    <section class="mb-12">
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Reportes & Finanzas</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Reporte de Ventas/Rentas -->
        <a href="{{ route('admin.reports.sales') }}"
           class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-chart-line"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Ventas/Rentas</h3>
          <p class="text-gray-600">Analiza desempeño y comisiones.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Ver reporte →</div>
        </a>

        <!-- Reporte de Visitas -->
        <a href="{{ route('admin.reports.visits') }}"
           class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-person-walking"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Visitas</h3>
          <p class="text-gray-600">Demanda y seguimiento de visitas.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Ver reporte →</div>
        </a>

        <!-- Comisiones -->
        <a href="{{ route('admin.commissions.index') }}"
           class="group bg-white rounded-xl shadow-md hover:shadow-lg transition p-6">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-hand-holding-dollar"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Control de Comisiones</h3>
          <p class="text-gray-600">Configura reglas y tasas del sistema.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Configurar →</div>
        </a>
      </div>

      <!-- Export rápido alineado a la derecha -->
      <div class="mt-6 flex justify-end">
        <form method="GET" action="{{ route('admin.reports.properties.export') }}">
          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <i class="fa-solid fa-file-excel"></i>
            Exportar comparativa en Excel
          </button>
        </form>
      </div>
    </section>

    <!-- Enlaces rápidos secundarios -->
    <section>
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Atajos</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('properties.map') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 text-center">
          <div class="text-blue-500 text-2xl mb-2"><i class="fas fa-map-location-dot"></i></div>
          <div class="font-semibold">Mapa</div>
          <div class="text-sm text-gray-600 mt-1">Explora propiedades en el mapa</div>
        </a>

        <a href="{{ route('favorites.index') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 text-center">
          <div class="text-pink-500 text-2xl mb-2"><i class="fas fa-heart"></i></div>
          <div class="font-semibold">Favoritos</div>
          <div class="text-sm text-gray-600 mt-1">Listado de favoritos</div>
        </a>

        <a href="{{ route('chat.index') }}"
           class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-6 text-center">
          <div class="text-indigo-500 text-2xl mb-2"><i class="fas fa-comments"></i></div>
          <div class="font-semibold">Mensajería</div>
          <div class="text-sm text-gray-600 mt-1">Contacta clientes y agentes</div>
        </a>


      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
          <div class="flex items-center text-white font-bold text-xl mb-4">
            <i class="fas fa-home mr-2"></i>
            <span>SIN BECA NO HAY RENTA</span>
          </div>
          <p class="text-gray-300 mb-4">
            Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades
            con sus futuros dueños de manera eficiente y profesional.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <div>
          <h3 class="font-semibold text-lg mb-4">Enlaces rápidos</h3>
          <ul class="space-y-2">
            <li><a href="{{ route('admin.properties.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Propiedades</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Usuarios</a></li>
            <li><a href="{{ route('admin.agent-applications.index') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Solicitudes</a></li>
            <li><a href="{{ route('admin.reports.sales') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Reportes</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-lg mb-4">Contacto</h3>
          <ul class="space-y-2 text-gray-300">
            <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> soporte@sinbeca.com</li>
            <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
            <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Ciudad, País</li>
          </ul>
        </div>
      </div>

      <div class="border-t border-gray-700 mt-8 pt-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-300 text-sm">&copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Privacidad</a>
            <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Términos</a>
            <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Cookies</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- JS: dropdown Perfil -->
  <script>
    (function () {
      const btn = document.getElementById('profile-menu-btn');
      const menu = document.getElementById('profile-menu');
      if (!btn || !menu) return;

      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
      });
      document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && e.target !== btn) {
          menu.classList.add('hidden');
        }
      });
    })();
  </script>
</body>
</html>
