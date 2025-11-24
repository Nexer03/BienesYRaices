<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sugerencias de clientes</title>
  <script>
    (function () {
      try {
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', theme === 'dark');
      } catch (e) {}
    })();
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' };</script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
  <x-main-header />

  <main class="max-w-6xl mx-auto px-6 py-10 space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">Retroalimentación privada de tus clientes</p>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-50 flex items-center gap-2">
          <i class="fa-solid fa-lightbulb text-amber-500"></i>
          Sugerencias recibidas
        </h1>
      </div>
      <form method="GET" class="flex items-center gap-3">
        <label for="type" class="text-sm text-gray-600 dark:text-gray-300">Filtrar por origen:</label>
        <select id="type" name="type" onchange="this.form.submit()"
          class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500">
          <option value="">Todas</option>
          <option value="reservation" @selected($type === 'reservation')>Rentas</option>
          <option value="visit" @selected($type === 'visit')>Visitas</option>
        </select>
      </form>
    </header>

    <section class="bg-white/80 dark:bg-gray-900/70 rounded-2xl shadow border border-gray-200 dark:border-gray-800 overflow-hidden">
      <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
          <i class="fa-solid fa-bell text-blue-500"></i>
          <span>Notificaciones privadas para visitas y rentas</span>
        </div>
        <span class="text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-100">{{ $suggestions->total() }} en total</span>
      </div>

      <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($suggestions as $suggestion)
          <article class="px-6 py-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-1">
              <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $suggestion->type === 'visit' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-100' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-100' }}">
                  {{ $suggestion->type === 'visit' ? 'Visita' : 'Renta' }}
                </span>
                <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1">
                  <i class="fa-regular fa-clock"></i>
                  {{ $suggestion->created_at?->diffForHumans() }}
                </span>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-50">{{ $suggestion->property?->title ?? 'Propiedad' }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-300">Cliente: {{ $suggestion->user?->name ?? '—' }}</p>
              <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ $suggestion->message }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ $suggestion->type === 'visit' ? 'Visita #' . $suggestion->visit_id : 'Reserva #' . $suggestion->reservation_id }}</p>
            </div>
          </article>
        @empty
          <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
            No tienes sugerencias registradas todavía.
          </div>
        @endforelse
      </div>

      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-800">
        {{ $suggestions->links() }}
      </div>
    </section>
  </main>
</body>
</html>
