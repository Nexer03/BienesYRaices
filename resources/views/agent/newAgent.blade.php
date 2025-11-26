<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro como Agente</title>

  {{-- Anti-flash: por defecto CLARO; si guardaste "dark", lo aplica --}}
  <script>
    (function () {
      try {
        if (localStorage.getItem('theme') === 'dark') {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark'); // claro por defecto
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
    /* Transición suave al cambiar de tema */
    html.theme-fade * {
      transition:
        background-color .35s ease,
        color .35s ease,
        border-color .35s ease,
        fill .35s ease;
    }

    /* Botón flotante de tema */
    #theme-toggle {
      transition:
        background-color .25s ease,
        color .25s ease,
        transform .25s ease,
        box-shadow .25s ease;
    }

    #theme-toggle:hover {
      transform: translateY(-2px);
      box-shadow: 0 16px 40px rgba(15, 23, 42, 0.55);
    }

    #theme-toggle.theme-bounce {
      transform: translateY(-1px) scale(1.04);
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.75);
    }

    #theme-toggle-icon {
      transition: transform .35s ease;
    }

    #theme-toggle-icon.theme-spin {
      transform: rotate(180deg);
    }

    .dark #theme-toggle {
      background-color: #020617 !important;
      color: #e5e7eb !important;
    }
  </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100">

  {{-- HEADER --}}
  <x-main-header />

  {{-- CONTENIDO PRINCIPAL --}}
  <main class="flex-1 flex items-center justify-center px-4 py-10 mt-20">
    <div
      class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 w-full max-w-lg
             border border-gray-200 dark:border-gray-700">
      <div class="text-center mb-6">
        <i class="fas fa-user-tie text-4xl text-blue-600 mb-2"></i>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          Registro como Agente
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">
          Completa los siguientes campos para enviar tu solicitud
        </p>
      </div>

      <form action="{{ route('agent.register.store') }}" method="POST"
            id="agentForm" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- RFC --}}
        <div>
          <label for="rfc" class="block font-medium text-gray-700 dark:text-gray-200 mb-1">
            <i class="fa-solid fa-id-card mr-2 text-blue-500"></i>RFC
          </label>
          <input
            type="text"
            name="rfc"
            id="rfc"
            placeholder="AAA000000AAA"
            pattern="[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}"
            required
            class="w-full border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-800
                   text-gray-900 dark:text-gray-100
                   rounded-lg px-4 py-2
                   focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                   uppercase"
          >
          <small class="text-gray-500 dark:text-gray-400 text-sm">
            Formato: 3–4 letras, 6 números, 3 caracteres
          </small>
        </div>

        {{-- CURP --}}
        <div>
          <label for="curp" class="block font-medium text-gray-700 dark:text-gray-200 mb-1">
            <i class="fa-solid fa-user-check mr-2 text-blue-500"></i>CURP
          </label>
          <input
            type="text"
            name="curp"
            id="curp"
            placeholder="AAAA000000HAAAAAA00"
            pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}"
            required
            class="w-full border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-800
                   text-gray-900 dark:text-gray-100
                   rounded-lg px-4 py-2
                   focus:ring-2 focus:ring-blue-400 focus:border-blue-400
                   uppercase"
          >
          <small class="text-gray-500 dark:text-gray-400 text-sm">
            Formato: 4 letras, 6 números, 1 letra (H/M), 5 letras, 2 caracteres
          </small>
        </div>

        {{-- INE frontal --}}
        <div class="mb-4">
          <label for="ine_front" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            <i class="fa-regular fa-id-card mr-1 text-blue-500"></i>
            Foto INE (frontal)
          </label>
          <input
            type="file"
            name="ine_front"
            id="ine_front"
            accept="image/*"
            required
            class="mt-1 block w-full text-sm
                   text-gray-900 dark:text-gray-100
                   border border-gray-300 dark:border-gray-600
                   rounded-lg shadow-sm
                   bg-white dark:bg-gray-800
                   file:mr-3 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-600 file:text-white
                   hover:file:bg-blue-700
                   file:cursor-pointer"
          >
        </div>

        {{-- INE reverso --}}
        <div class="mb-4">
          <label for="ine_back" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            <i class="fa-regular fa-id-card mr-1 text-blue-500"></i>
            Foto INE (reverso)
          </label>
          <input
            type="file"
            name="ine_back"
            id="ine_back"
            accept="image/*"
            required
            class="mt-1 block w-full text-sm
                   text-gray-900 dark:text-gray-100
                   border border-gray-300 dark:border-gray-600
                   rounded-lg shadow-sm
                   bg-white dark:bg-gray-800
                   file:mr-3 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-600 file:text-white
                   hover:file:bg-blue-700
                   file:cursor-pointer"
          >
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition"
        >
          <i class="fa-solid fa-paper-plane mr-2"></i>Enviar Solicitud
        </button>
      </form>
    </div>
  </main>

  {{-- FOOTER --}}
  <footer class="bg-white border-t border-gray-200 text-gray-600 dark:bg-gray-950 dark:border-gray-800 dark:text-gray-300 py-6 mt-auto">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
      <p class="text-sm">
        &copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.
      </p>
      <div class="flex space-x-4 mt-3 md:mt-0 text-gray-500 dark:text-gray-400">
        <a href="#" class="hover:text-blue-500"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="hover:text-blue-500"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-pink-500"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </footer>

  {{-- Botón Tema --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  {{-- VALIDACIONES --}}
  <script>
    const rfcInput  = document.getElementById('rfc');
    const curpInput = document.getElementById('curp');

    rfcInput.addEventListener('input', e => {
      e.target.value = e.target.value.toUpperCase();
      const pattern = /^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
      e.target.style.borderColor = (e.target.value && !pattern.test(e.target.value)) ? 'red' : '';
    });

    curpInput.addEventListener('input', e => {
      e.target.value = e.target.value.toUpperCase();
      const pattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/;
      e.target.style.borderColor = (e.target.value && !pattern.test(e.target.value)) ? 'red' : '';
    });
  </script>

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
