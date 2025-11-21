<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Mensajes del Agente</title>

  <!-- Anti-flash: tema guardado (CLARO por defecto) -->
  <script>
    (function () {
      try {
        var saved = localStorage.getItem('theme');
        if (!saved) { localStorage.setItem('theme','light'); saved='light'; }
        if (saved === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
      } catch (e) { document.documentElement.classList.remove('dark'); }
    })();
  </script>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script> tailwind.config = { darkMode: 'class' };</script>

  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* ===== Fondo base (claro) + overlay oscuro que hace fade ===== */
    :root{
      --bg1-light:#f9fafb; /* gray-50 */
      --bg2-light:#eef2f7;
      --bg1-dark:#0b1220;  /* tono oscuro */
      --bg2-dark:#0a0f1a;
      --text-light:#1f2937;
      --text-dark:#e5e7eb;
    }

    /* Base: gradiente claro */
    body{
      position: relative;
      min-height: 100vh;
      background-image: linear-gradient(180deg,var(--bg1-light) 0%,var(--bg2-light) 100%);
      color: var(--text-light);
      transition:
        color .45s ease,
        border-color .45s ease;
    }
    /* Texto en dark */
    html.dark body{ color: var(--text-dark); }

    /* Overlay oscuro que se ANIMA con opacity */
    body::before{
      content:"";
      position: fixed;
      inset: 0;
      z-index: -1;
      background-image: linear-gradient(180deg,var(--bg1-dark) 0%,var(--bg2-dark) 100%);
      opacity: 0;
      transition: opacity .65s ease;
      pointer-events: none; /* no bloquea clics */
    }
    html.dark body::before{ opacity: 1; }

    /* Suaviza cambios en elementos */
    html.theme-fade *{
      transition:
        background-color .45s ease,
        color .45s ease,
        border-color .45s ease,
        box-shadow .45s ease;
    }

    /* Botón: pequeña animación */
    #theme-toggle{ transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, color .25s ease; }
    #theme-toggle.bounce{ transform: translateY(-1px) scale(1.03); box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    #theme-toggle-icon{ transition: transform .35s ease, opacity .2s ease; }
    #theme-toggle-icon.spin{ transform: rotate(180deg); }

    /* Respeto a usuarios con movimiento reducido */
    @media (prefers-reduced-motion: reduce){
      body, body::before, html.theme-fade * { transition: none !important; }
    }
  </style>
</head>

<body class="text-gray-800 dark:text-gray-100">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-5xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
        <i class="fa-solid fa-comments text-blue-600"></i> Mensajes (Agente)
      </h2>
      <a href="{{ url()->previous() }}"
         class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center gap-1">
        <i class="fa-solid fa-arrow-left"></i> Volver
      </a>
    </div>

    {{-- MENSAJE FLASH --}}
    @if (session('info'))
      <div class="mb-6 p-4 rounded-lg border border-blue-200 bg-blue-50 text-blue-800
                  dark:border-blue-900/40 dark:bg-blue-900/30 dark:text-blue-200">
        <i class="fa-solid fa-circle-info mr-2"></i> {{ session('info') }}
      </div>
    @endif

    {{-- SIN CONVERSACIONES --}}
    @if ($conversations->isEmpty())
      <div class="text-center py-10 bg-white/90 border border-gray-200 rounded-xl shadow-sm
                  dark:bg-gray-900/90 dark:border-gray-800">
        <i class="fa-regular fa-comments text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
        <p class="text-gray-600 dark:text-gray-300 text-lg">Aún no tienes conversaciones con clientes.</p>
      </div>

    {{-- CONVERSACIONES --}}
    @else
      <div class="space-y-3">
        @foreach ($conversations as $c)
          @php
            $other = $c->client; // vista del agente
          @endphp

          <a href="{{ route('chat.open', $c) }}"
             class="flex items-center justify-between w-full rounded-lg border border-gray-200 bg-white/95 p-4 hover:bg-white transition shadow-sm
                    dark:border-gray-800 dark:bg-gray-900/95 dark:hover:bg-gray-800/95">
            <div class="min-w-0 flex-1">
              <div class="font-semibold text-gray-800 dark:text-gray-100 truncate">
                <i class="fa-solid fa-user text-gray-500 dark:text-gray-400 mr-2"></i>
                {{ $other->name ?? 'Cliente' }}
                @if($c->property)
                  <span class="text-gray-500 dark:text-gray-400 font-normal">— {{ $c->property->title }}</span>
                @endif
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Conversación con el cliente
              </div>
            </div>

            @if(($c->unread_count ?? 0) > 0)
              <span class="ml-3 inline-flex items-center justify-center rounded-full text-xs font-semibold
                           bg-blue-600 text-white px-3 py-1.5 shadow-sm">
                {{ $c->unread_count }}
              </span>
            @endif
          </a>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $conversations->links() }}
      </div>
    @endif
  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

  {{-- Botón Tema (sol = claro, luna = oscuro) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <script>
    (function () {
      const html  = document.documentElement;
      const btn   = document.getElementById('theme-toggle');
      const icon  = document.getElementById('theme-toggle-icon');
      const label = btn?.querySelector('span');

      function setIconAndLabel() {
        const isDark = html.classList.contains('dark');
        if (!icon || !label) return;
        icon.classList.remove('fa-sun','fa-moon');
        icon.classList.add(isDark ? 'fa-moon' : 'fa-sun'); // luna = oscuro, sol = claro
        label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
      }

      function fadeAllStart() {
        html.classList.add('theme-fade');
        setTimeout(() => html.classList.remove('theme-fade'), 500);
      }

      function animateButton() {
        if (!btn || !icon) return;
        btn.classList.add('bounce'); icon.classList.add('spin');
        setTimeout(() => { btn.classList.remove('bounce'); icon.classList.remove('spin'); }, 350);
      }

      function apply(mode){
        const isDark = mode === 'dark';
        fadeAllStart();                 // activa transiciones
        html.classList.toggle('dark', isDark); // el overlay hace el fade del fondo
        try { localStorage.setItem('theme', mode); } catch(e){}
        setIconAndLabel();
        animateButton();
      }

      // Estado inicial
      setIconAndLabel();

      // Toggle
      btn?.addEventListener('click', () => {
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>
</body>
</html>
