<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva visita - SIN BECA NO HAY RENTA</title>

  <!-- Anti-flash: claro por defecto; si guardaste 'dark', lo aplica antes de pintar -->
  <script>
    (function () {
      try {
        var saved = localStorage.getItem('theme');
        if (!saved) { localStorage.setItem('theme','light'); saved = 'light'; }
        if (saved === 'dark') document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
      } catch (e) { document.documentElement.classList.remove('dark'); }
    })();
  </script>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script> tailwind.config = { darkMode: 'class' };</script>

  <!-- Iconos -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* ===== Fondo base (claro) + overlay oscuro con FADE real ===== */
    :root{
      --bg1-light:#f7fafc; /* gray-50 aprox */
      --bg2-light:#edf2f7;
      --bg1-dark:#0b1220;  /* degradado oscuro */
      --bg2-dark:#0a0f1a;
    }
    body{
      position: relative;
      min-height: 100vh;
      background-image: linear-gradient(180deg,var(--bg1-light) 0%,var(--bg2-light) 100%);
      transition: color .45s ease, border-color .45s ease, box-shadow .45s ease;
    }
    body::before{
      content:"";
      position: fixed; inset:0; z-index:-1;
      background-image: linear-gradient(180deg,var(--bg1-dark) 0%,var(--bg2-dark) 100%);
      opacity: 0; transition: opacity .65s ease; pointer-events:none;
    }
    html.dark body::before{ opacity: 1; }

    /* Suaviza componentes al cambiar tema */
    html.theme-fade *{
      transition:
        background-color .45s ease,
        color .45s ease,
        border-color .45s ease,
        box-shadow .45s ease,
        fill .45s ease;
    }

    /* Botón tema animación */
    #theme-toggle{ transition: transform .25s ease, box-shadow .25s ease, background-color .25s ease, color .25s ease; }
    #theme-toggle.bounce{ transform: translateY(-1px) scale(1.03); box-shadow: 0 18px 35px rgba(0,0,0,.18); }
    #theme-toggle-icon{ transition: transform .35s ease, opacity .2s ease; }
    #theme-toggle-icon.spin{ transform: rotate(180deg); }

    @media (prefers-reduced-motion: reduce){
      body, body::before, html.theme-fade * { transition: none !important; }
    }
  </style>
</head>

<body class="bg-transparent text-gray-800 dark:text-gray-100 min-h-screen flex flex-col">

  <!-- HEADER -->
  <x-main-header />

  <!-- MAIN -->
  <main class="flex-grow max-w-3xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10">

    <!-- TÍTULO -->
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8 flex items-center gap-3">
      <i class="fa-solid fa-calendar-plus text-blue-500"></i>
      Nueva visita
    </h1>

    <!-- ERRORES -->
    @if($errors->any())
      <div class="mb-6 bg-red-50 dark:bg-red-900/40 border border-red-300 dark:border-red-700
                  text-red-700 dark:text-red-200 px-4 py-3 rounded-xl">
        <ul class="space-y-1 list-disc ml-5">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- CARD FORMULARIO -->
    <form method="POST" action="{{ route('agent.visits.store') }}"
          class="bg-white/90 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800
                 shadow-xl rounded-2xl p-8 space-y-6 backdrop-blur-md">
      @csrf

      <!-- PROPIEDAD -->
      <div>
        <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Propiedad</label>
        <select name="property_id" required
                class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                       px-3 py-2 text-gray-800 dark:text-gray-200
                       focus:ring-blue-500 focus:border-blue-500 outline-none">
          <option value="">Selecciona una propiedad</option>
          @foreach($properties as $id => $title)
            <option value="{{ $id }}" @selected(old('property_id')==$id)>{{ $title }}</option>
          @endforeach
        </select>
        @error('property_id')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- CLIENTE -->
      <div>
        <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Cliente</label>
        <select name="client_id" required
                class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                       px-3 py-2 text-gray-800 dark:text-gray-200
                       focus:ring-blue-500 focus:border-blue-500 outline-none">
          <option value="">Selecciona un cliente</option>
          @foreach($clients as $id => $name)
            <option value="{{ $id }}" @selected(old('client_id')==$id)>{{ $name }}</option>
          @endforeach
        </select>
        @error('client_id')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- FECHA Y HORA -->
      <div>
        <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Fecha y hora</label>
        <input type="datetime-local"
               name="visit_date"
               value="{{ old('visit_date') }}"
               required
               class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                      px-3 py-2 text-gray-800 dark:text-gray-200
                      focus:ring-blue-500 focus:border-blue-500 outline-none">
        @error('visit_date')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- NOTAS -->
      <div>
        <label class="block text-gray-700 dark:text-gray-300 font-medium mb-1">Notas</label>
        <textarea name="notes" rows="3"
                  class="w-full rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700
                         px-3 py-2 text-gray-800 dark:text-gray-200
                         focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('notes') }}</textarea>
        @error('notes')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- BOTONES -->
      <div class="flex justify-end gap-3 pt-4">
        <a href="{{ url()->previous() }}"
           class="px-5 py-2 rounded-xl border border-gray-300 dark:border-gray-700
                  text-gray-700 dark:text-gray-300
                  hover:bg-gray-100 dark:hover:bg-gray-800 transition">
          Cancelar
        </a>

        <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow transition">
          Guardar
        </button>
      </div>
    </form>
  </main>

  <!-- FOOTER -->
  <x-main-footer />

  <!-- Botón Tema (sol = claro, luna = oscuro) -->
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <!-- Toggle de tema con fade y animación -->
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
        icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
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
        fadeAllStart();                           // activa transiciones suaves
        html.classList.toggle('dark', isDark);    // overlay hace fade del fondo
        try { localStorage.setItem('theme', mode); } catch(e){}
        setIconAndLabel();
        animateButton();
      }

      // Estado inicial de icono/label
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
