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
          <!-- ICONO ARREGLADO -->
          <div class="text-blue-500 text-3xl">
            <i class="fas fa-user-check"></i>
          </div>
          
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

   
  </main>
  <!-- FOOTER -->
  <x-main-footer />

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
