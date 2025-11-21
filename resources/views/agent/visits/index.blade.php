<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Visitas y Reservaciones</title>

  <!-- Anti-flash: aplica tema guardado (CLARO por defecto) antes de pintar -->
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

  <!-- Alpine -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <!-- Iconos -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

  <style>
    /* ===== Fondo base (claro) + overlay oscuro con FADE real ===== */
    :root{
      --bg1-light:#f7fafc; /* near gray-50 */
      --bg2-light:#e9eef5;
      --bg1-dark:#0b1220;  /* dark gradient */
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

    /* Suaviza todos los componentes en cambios de tema */
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

    /* FullCalendar temas (v6 usa CSS vars) */
    :root{
      --fc-border-color:#e5e7eb;            /* gray-200 */
      --fc-neutral-bg-color: #f9fafb;       /* gray-50 */
      --fc-page-bg-color: transparent;
      --fc-today-bg-color: rgba(59,130,246,.12); /* blue-500/12 */
      --fc-event-bg-color:#3b82f6;          /* blue-500 */
      --fc-event-border-color:#2563eb;      /* blue-600 */
      --fc-event-text-color:#fff;
      --fc-button-bg-color:#3b82f6;
      --fc-button-border-color:#2563eb;
      --fc-button-text-color:#fff;
    }
    html.dark :root{
      --fc-border-color:#374151;            /* gray-700 */
      --fc-neutral-bg-color:#0f172a;        /* slate-900 */
      --fc-page-bg-color: transparent;
      --fc-today-bg-color: rgba(59,130,246,.18);
      --fc-event-bg-color:#60a5fa;          /* blue-400 */
      --fc-event-border-color:#3b82f6;      /* blue-500 */
      --fc-event-text-color:#0b1220;        /* legible sobre azul claro */
      --fc-button-bg-color:#1f2937;         /* gray-800 */
      --fc-button-border-color:#374151;     /* gray-700 */
      --fc-button-text-color:#e5e7eb;       /* gray-200 */
    }

    @media (prefers-reduced-motion: reduce){
      body, body::before, html.theme-fade * { transition: none !important; }
    }
  </style>

  <!-- Alpine store: tab por defecto -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('activeTab', 'visits');
    });
  </script>
</head>

<body class="min-h-screen text-gray-800 dark:text-gray-100 flex flex-col">

  {{-- HEADER --}}
  <x-main-header />

  <main class="flex-1 max-w-7xl mx-auto px-6 py-12" x-data>
    <!-- HEADER -->
    <header class="mb-12">
      <h1 class="text-4xl font-extrabold text-gray-800 dark:text-gray-100 tracking-tight mb-2">
        Gestión de visitas y reservaciones
      </h1>
      <p class="text-gray-600 dark:text-gray-300 text-lg">
        Consulta tus visitas agendadas y reservaciones desde un solo panel.
      </p>
    </header>

    <!-- SWITCH ENTRE TABS -->
    <div class="flex justify-center mb-12">
      <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border border-gray-200 dark:border-gray-800 shadow-lg rounded-2xl flex p-1">
        <button
          @click="$store.activeTab = 'visits'"
          :class="$store.activeTab === 'visits' ? 'bg-blue-600 text-white shadow px-6' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 px-6'"
          class="py-2 rounded-xl text-sm font-semibold transition-all">
          <i class="fa-solid fa-calendar-check mr-2"></i> Visitas
        </button>

        <button
          @click="$store.activeTab = 'reservations'"
          :class="$store.activeTab === 'reservations' ? 'bg-blue-600 text-white shadow px-6' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 px-6'"
          class="py-2 rounded-xl text-sm font-semibold transition-all">
          <i class="fa-solid fa-calendar-days mr-2"></i> Reservaciones
        </button>
      </div>
    </div>

    <!-- CALENDARIO VISITAS -->
    <section x-show="$store.activeTab === 'visits'" x-transition>
      <div class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-8 mb-10">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fa-solid fa-house mr-2"></i> Calendario de visitas
          </h2>
          <span class="text-sm text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-lg">Solo propiedades en venta</span>
        </div>
        <div id="visits-calendar" class="bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-800"></div>
      </div>
    </section>

    <!-- CALENDARIO RESERVACIONES -->
    <section x-show="$store.activeTab === 'reservations'" x-transition>
      <div class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-8 mb-10">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
            <i class="fa-solid fa-bookmark mr-2"></i> Calendario de reservaciones
          </h2>
          <span class="text-sm text-gray-500 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-lg">Activas y pasadas</span>
        </div>
        <div id="reservations-calendar" class="bg-white dark:bg-gray-900 rounded-xl p-3 border border-gray-100 dark:border-gray-800"></div>
      </div>
    </section>

    <!-- LISTA DE VISITAS -->
    <section class="bg-white/70 dark:bg-gray-900/70 backdrop-blur-md border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center">
          <i class="fa-solid fa-list mr-2"></i> Listado de visitas
        </h2>

        <a href="{{ route('agent.visits.create') }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow font-semibold transition-all">
          <i class="fa-solid fa-plus mr-1"></i> Nueva visita
        </a>
      </div>

      <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-gray-100/60 dark:bg-gray-800/60 text-gray-700 dark:text-gray-200">
              <th class="px-4 py-3 text-left font-semibold">Fecha / hora</th>
              <th class="px-4 py-3 text-left font-semibold">Propiedad</th>
              <th class="px-4 py-3 text-left font-semibold">Cliente</th>
              <th class="px-4 py-3 text-left font-semibold">Estado</th>
              <th class="px-4 py-3 text-left font-semibold text-right">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($visits as $visit)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
              <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $visit->visit_date?->format('Y-m-d H:i') ?? '—' }}</td>
              <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $visit->property?->title ?? '—' }}</td>
              <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $visit->client?->name ?? '—' }}</td>

              <td class="px-4 py-3">
                <form action="{{ route('agent.visits.status', $visit) }}" method="POST">
                  @csrf @method('PATCH')
                  <select name="status" onchange="this.form.submit()"
                          class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(['pending'=>'Pendiente','confirmed'=>'Confirmada','completed'=>'Completada','cancelled'=>'Cancelada'] as $key => $label)
                      <option value="{{ $key }}" @selected($visit->status === $key)>{{ $label }}</option>
                    @endforeach
                  </select>
                </form>
              </td>

              <td class="px-4 py-3 text-right">
                <a href="{{ route('agent.visits.edit', $visit) }}" class="text-blue-600 hover:underline font-medium">Editar</a>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <form action="{{ route('agent.visits.destroy', $visit) }}" method="POST" class="inline"
                      onsubmit="return confirm('¿Eliminar esta visita?');">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline font-medium" type="submit">Eliminar</button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-gray-500 dark:text-gray-400 py-6">No tienes visitas registradas.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($visits, 'links'))
        <div class="mt-8">
          {{ $visits->links() }}
        </div>
      @endif
    </section>
  </main>

  {{-- FOOTER --}}
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

  <!-- Calendarios + Fix de tamaño al cambiar de tab -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // VISITS
      window.visitsCalendar = new FullCalendar.Calendar(document.getElementById('visits-calendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'es',
        nowIndicator: true,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
        eventSources: [{ url: '{{ route('agent.visits.feed') }}', method: 'GET' }],
      });
      window.visitsCalendar.render();

      // RESERVATIONS
      window.reservationsCalendar = new FullCalendar.Calendar(document.getElementById('reservations-calendar'), {
        initialView: 'dayGridMonth',
        height: 'auto',
        locale: 'es',
        nowIndicator: true,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        eventSources: [{ url: '{{ route('agent.reservations.feed') }}', method: 'GET' }],
      });
      window.reservationsCalendar.render();
    });

    // Forzar updateSize tras cambiar de tab con Alpine
    document.addEventListener('alpine:init', () => {
      Alpine.effect(() => {
        const tab = Alpine.store('activeTab');
        setTimeout(() => {
          if (tab === 'visits' && window.visitsCalendar) window.visitsCalendar.updateSize();
          if (tab === 'reservations' && window.reservationsCalendar) window.reservationsCalendar.updateSize();
        }, 80);
      });
    });
  </script>

  <!-- Toggle de tema con fade y animación de botón -->
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
        // Reparar tamaño de calendarios tras cambio de tema
        setTimeout(() => {
          window.visitsCalendar?.updateSize();
          window.reservationsCalendar?.updateSize();
        }, 60);
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
