<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Mis Favoritos</title>

  <!-- Anti-flash: por defecto CLARO; si guardaste 'dark', lo aplica -->
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

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>

  <!-- Alpine, SweetAlert, Iconos -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">
  {{-- HEADER --}}
  <x-main-header />

  {{-- Botón Tema (flotante) --}}
  <button id="theme-toggle"
    class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
           bg-white text-gray-800 hover:bg-gray-100
           dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700
           transition-colors duration-200"
    aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <main class="flex-1 max-w-7xl mx-auto px-6 py-12">
    {{-- Encabezado --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Mis propiedades favoritas</h1>
        <p class="text-gray-500 dark:text-gray-300 text-sm">Guarda y organiza tus propiedades de interés</p>
      </div>

      {{-- Filtros de tipo --}}
      <div class="inline-flex bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full overflow-hidden shadow-sm">
        <a href="{{ route('favorites.index') }}"
           class="px-5 py-2 text-sm font-semibold transition
           {{ $type === null ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800' }}">
           Todos
        </a>
        <a href="{{ route('favorites.index', ['type'=>'sale']) }}"
           class="px-5 py-2 text-sm font-semibold transition
           {{ $type === 'sale' ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800' }}">
           Venta
        </a>
        <a href="{{ route('favorites.index', ['type'=>'rent']) }}"
           class="px-5 py-2 text-sm font-semibold transition
           {{ $type === 'rent' ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-800' }}">
           Renta
        </a>
      </div>
    </div>

    {{-- Mensajes flash --}}
    @if (session('success'))
      <div class="bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6 border border-green-300 dark:border-green-700">
        {{ session('success') }}
      </div>
    @endif

    {{-- Estado vacío --}}
    @if($props->isEmpty())
      <div class="text-center bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl py-16 shadow-sm">
        <div class="text-5xl text-blue-400 mb-3">☆</div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Aún no tienes favoritos</h2>
        <p class="text-gray-500 dark:text-gray-300 text-sm">Explora propiedades y agrégalas para verlas aquí.</p>
      </div>
    @else
      {{-- Grid de propiedades --}}
      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($props as $p)
          @php
            $img = $p->images->first()->image_path ?? null;
            $isRent = $p->listing_type === 'rent';
          @endphp

          <div id="fav-card-{{ $p->id }}" data-fav-card
               class="group relative bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow hover:shadow-lg transition">
            {{-- Imagen --}}
            <div class="relative h-48 bg-gray-100 dark:bg-gray-800">
              @if($img)
                <img src="{{ asset('storage/'.$img) }}" alt="{{ $p->title }}"
                     class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
              @else
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 text-sm">Sin imagen</div>
              @endif

              {{-- Badge tipo --}}
              <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold rounded-full
                {{ $isRent ? 'bg-blue-600/90 text-white' : 'bg-green-600/90 text-white' }}">
                {{ $isRent ? 'Renta' : 'Venta' }}
              </span>

              {{-- Botón eliminar --}}
              <form method="POST" action="{{ route('favorites.destroy', $p) }}"
                    class="absolute top-3 right-3 fav-remove-form" data-prop-id="{{ $p->id }}">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="bg-white/90 dark:bg-gray-900/90 hover:bg-red-500 hover:text-white transition
                               rounded-full p-2 shadow-sm text-gray-700 dark:text-gray-200"
                        title="Quitar de favoritos">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </div>

            {{-- Info --}}
            <div class="p-5 flex flex-col h-52">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate" title="{{ $p->title }}">
                {{ $p->title }}
              </h3>
              @if($p->location)
                <p class="text-gray-500 dark:text-gray-300 text-sm mb-2 truncate" title="{{ $p->location }}">
                  <i class="fa-solid fa-location-dot mr-1"></i> {{ $p->location }}
                </p>
              @endif

              <div class="flex items-baseline gap-1 mb-3">
                <span class="text-xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($p->price,0) }}</span>
                @if($isRent)
                  <span class="text-sm text-gray-500 dark:text-gray-300">/día</span>
                @endif
              </div>

              <div class="flex flex-wrap gap-2 mb-4">
                @if($p->bedrooms)
                  <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-2 py-1 rounded-md text-xs">
                    <i class="fa-solid fa-bed mr-1"></i>{{ $p->bedrooms }}
                  </span>
                @endif
                @if($p->bathrooms)
                  <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-2 py-1 rounded-md text-xs">
                    <i class="fa-solid fa-bath mr-1"></i>{{ $p->bathrooms }}
                  </span>
                @endif
                @if($p->city)
                  <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-2 py-1 rounded-md text-xs">
                    <i class="fa-solid fa-city mr-1"></i>{{ $p->city }}
                  </span>
                @endif
              </div>

              <div class="mt-auto flex gap-2">
                <a href="{{ route('properties.show', $p) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg w-full transition">
                  Ver
                </a>

                @can('update', $p)
                  <a href="{{ route('properties.edit', $p) }}"
                     class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
                            text-gray-700 dark:text-gray-200 text-sm px-3 py-2 rounded-lg transition"
                     title="Editar">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                @endcan
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Paginación --}}
      <div class="mt-10">
        {{ $props->links() }}
      </div>
    @endif
  </main>

  {{-- FOOTER --}}
  <x-main-footer />

  {{-- SCRIPT SweetAlert (NO LO TOQUÉ) --}}
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.fav-remove-form button').forEach(btn => {
      btn.addEventListener('click', async e => {
        const form = e.currentTarget.closest('.fav-remove-form');
        const url = form.action;
        const id = form.dataset.propId;
        const card = document.getElementById(`fav-card-${id}`);

        const confirm = await Swal.fire({
          title: 'Quitar de favoritos',
          text: 'Esta propiedad se eliminará de tu lista.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, quitar',
          cancelButtonText: 'Cancelar',
          reverseButtons: true,
          buttonsStyling: false,
          customClass: {
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg mx-2',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg'
          }
        });

        if (!confirm.isConfirmed) return;

        try {
          const resp = await fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
          });

          if (!resp.ok) throw new Error('HTTP ' + resp.status);
          card.remove();

          Swal.fire({
            icon: 'success',
            title: 'Eliminada',
            text: 'Se quitó de tus favoritos.',
            timer: 1400,
            showConfirmButton: false
          });

          if (!document.querySelector('[data-fav-card]')) location.reload();
        } catch {
          Swal.fire({
            icon: 'error',
            title: 'Ups...',
            text: 'No pudimos quitarla. Intenta de nuevo.'
          });
        }
      });
    });
  });
  </script>

  {{-- SCRIPT MODO OSCURO (simple, sin romper nada) --}}
  <script>
    (function () {
      const html  = document.documentElement;
      const btn   = document.getElementById('theme-toggle');
      const icon  = document.getElementById('theme-toggle-icon');
      const label = btn ? btn.querySelector('span') : null;

      function syncUI() {
        const isDark = html.classList.contains('dark');
        if (icon) {
          icon.classList.remove('fa-sun', 'fa-moon');
          icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
        }
        if (label) {
          label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
        }
      }

      // Estado inicial desde localStorage
      try {
        if (localStorage.getItem('theme') === 'dark') {
          html.classList.add('dark');
        } else {
          html.classList.remove('dark');
        }
      } catch (e) {}

      syncUI();

      if (btn) {
        btn.addEventListener('click', () => {
          const isDark = !html.classList.contains('dark');
          html.classList.toggle('dark', isDark);
          try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
          } catch (e) {}
          syncUI();
        });
      }
    })();
  </script>
</body>
</html>
