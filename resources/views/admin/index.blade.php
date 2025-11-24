<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel Administrador - SIN BECA NO HAY RENTA</title>

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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
      /* Fade suave para todo al cambiar de tema */
      html.theme-fade * {
          transition:
              background-color .35s ease,
              color .35s ease,
              border-color .35s ease,
              fill .35s ease;
      }

      /* Botón de tema: animación */
      #theme-toggle {
          transition: background-color .25s ease,
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

      .dark footer {
          background-color: #020617 !important; /* slate-950 */
      }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">
  <x-main-header />

  {{-- Botón Tema (igual que en home) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
      <i id="theme-toggle-icon" class="fa-solid"></i>
      <span class="text-sm font-medium"></span>
  </button>

  <!-- Contenido principal -->
  <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Encabezado -->
    <section class="text-center mb-12">
      <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
        Panel de Administrador
      </h1>
      <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
        Administra propiedades, usuarios, solicitudes y reportes desde un solo lugar.
      </p>
    </section>

    <!-- Acciones clave arriba -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <!-- Gestionar Propiedades -->
      <a href="{{ route('admin.properties.index') }}"
         class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl"><i class="fas fa-building"></i></div>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Gestionar Propiedades</h3>
        <p class="text-gray-600 dark:text-gray-300">Ver, filtrar y eliminar propiedades del sistema.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>

      <!-- Gestionar Usuarios -->
      <a href="{{ route('admin.users.index') }}"
         class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl"><i class="fas fa-users-cog"></i></div>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Gestionar Usuarios</h3>
        <p class="text-gray-600 dark:text-gray-300">Ver, editar, filtrar y eliminar usuarios.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>

      <!-- Solicitudes de Agentes -->
      <a href="{{ route('admin.agent-applications.index') }}"
         class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
        <div class="flex items-center justify-between mb-3">
          <div class="text-blue-500 text-3xl">
            <i class="fas fa-user-check"></i>
          </div>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Solicitudes de Agentes</h3>
        <p class="text-gray-600 dark:text-gray-300">Revisar, aprobar o rechazar solicitudes.</p>
        <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Entrar →</div>
      </a>
    </div>

    <!-- Reportes y Finanzas -->
    <section class="mb-12">
      <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Reportes & Finanzas</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Reporte de Ventas/Rentas -->
        <a href="{{ route('admin.reports.sales') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-chart-line"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Reporte de Ventas/Rentas</h3>
          <p class="text-gray-600 dark:text-gray-300">Analiza desempeño y comisiones.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Ver reporte →</div>
        </a>

        <!-- Reporte de Visitas -->
        <a href="{{ route('admin.reports.visits') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-person-walking"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Reporte de Visitas</h3>
          <p class="text-gray-600 dark:text-gray-300">Demanda y seguimiento de visitas.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Ver reporte →</div>
        </a>

        <!-- Comisiones -->
        <a href="{{ route('admin.commissions.index') }}"
           class="group bg-white dark:bg-gray-900 rounded-xl shadow-md hover:shadow-lg transition p-6 border border-transparent dark:border-gray-800">
          <div class="text-blue-500 text-3xl mb-3">
            <i class="fas fa-hand-holding-dollar"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Control de Comisiones</h3>
          <p class="text-gray-600 dark:text-gray-300">Configura reglas y tasas del sistema.</p>
          <div class="mt-4 text-blue-500 group-hover:text-blue-600 font-medium">Configurar →</div>
        </a>
      </div>

      <!-- Export rápido alineado a la derecha -->
      <div class="mt-6 flex justify-end">
        <form method="GET" action="{{ route('admin.reports.properties.export') }}">
          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <i class="fa-solid fa-file-excel"></i>
            Exportar ventas y rentas en Excel
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

  {{-- Lógica del botón de tema (igual que en home) --}}
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

          // Estado inicial de icono/texto según clase actual del <html>
          setIconAndLabel();

          btn?.addEventListener('click', () => {
              const next = html.classList.contains('dark') ? 'light' : 'dark';
              apply(next);
          });
      })();
  </script>
</body>
</html>
