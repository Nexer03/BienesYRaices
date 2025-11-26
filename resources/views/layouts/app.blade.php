<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>
    (() => {
      try {
        const mode = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', mode === 'dark');
      } catch (e) {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>

  {{-- Anti-flash: aplica el tema guardado ANTES de cargar CSS --}}
  <script>
    (function () {
      try {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark') {
          document.documentElement.classList.add('dark');
        } else if (stored === 'light') {
          document.documentElement.classList.remove('dark');
        }
      } catch (e) {
        document.documentElement.classList.remove('dark');
      }
    })();
  </script>

  @vite(['resources/js/app.js'])

  <title>@yield('title', 'SIN BECA NO HAY RENTA')</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
@php /** @var \App\Models\User|null $user */ $user = auth()->user(); @endphp

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
  <div class="container container-narrow">
    <a class="navbar-brand text-primary fw-bold" href="{{ route('home') }}">
      <i class="bi bi-house-door-fill me-1"></i> Sin beca <span class="text-dark">no hay renta</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('properties.index') }}">Propiedades</a></li>
        @if(Route::has('agent.visits.index'))
          <li class="nav-item"><a class="nav-link" href="{{ route('agent.visits.index') }}">Visitas</a></li>
        @endif
      </ul>

      <ul class="navbar-nav ms-auto">
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i> {{ $user?->name ?? 'Mi cuenta' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Perfil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Salir</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item"><a class="btn btn-primary" href="{{ route('login') }}">Iniciar sesión</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main class="container container-narrow py-4">
  {{-- Flash messages --}}
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  @isset($header)
    <div class="mb-4">
      {{ $header }}
    </div>
  @endisset

  @isset($slot)
    {{ $slot }}
  @else
    @yield('content')
  @endisset
</main>

<footer class="py-4 border-top bg-white">
  <div class="container container-narrow text-center text-muted small">
    © {{ date('Y') }} Sin beca no hay renta
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- 🎨 Estilos de layout + modo oscuro (se ponen al final para que ganen siempre) --}}
<style>
  html, body {
    min-height: 100%;
  }

  .container-narrow {
    max-width: 1120px;
  }

  /* MODO CLARO (por defecto) */
  body {
    background: #f7f8fa;
    color: #111827;
    transition: background-color .35s ease, color .35s ease;
  }

  /* MODO OSCURO: cuando <html> tiene class="dark" (tu botón ya hace eso) */
  html.dark body {
    background: #020617;        /* fondo oscuro */
    color: #e5e7eb;
  }

  html.dark .navbar {
    background-color: #020617 !important;
    border-bottom-color: #111827 !important;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.75);
  }

  html.dark .navbar .navbar-brand {
    color: #60a5fa !important;
  }

  html.dark .navbar .navbar-brand .text-dark {
    color: #e5e7eb !important;
  }

  html.dark .navbar .nav-link {
    color: #e5e7eb !important;
  }

  html.dark .navbar .nav-link:hover {
    color: #bfdbfe !important;
  }

  html.dark .navbar .btn.btn-primary {
    background-color: #2563eb;
    border-color: #2563eb;
  }

  /* Dropdown usuario */
  html.dark .dropdown-menu {
    background-color: #020617;
    color: #e5e7eb;
    border-color: #111827;
  }

  html.dark .dropdown-item {
    color: #e5e7eb;
  }

  html.dark .dropdown-item:hover {
    background-color: #111827;
    color: #f9fafb;
  }

  html.dark .dropdown-divider {
    border-color: #1f2937;
  }

  /* Alertas */
  html.dark .alert-success {
    background-color: #14532d;
    border-color: #166534;
    color: #bbf7d0;
  }

  html.dark .alert-danger {
    background-color: #7f1d1d;
    border-color: #b91c1c;
    color: #fee2e2;
  }

  /* Footer */
  html.dark footer {
    background-color: #020617 !important;
    border-top-color: #111827 !important;
  }

  html.dark footer .text-muted {
    color: #9ca3af !important;
  }
</style>
</body>
</html>
