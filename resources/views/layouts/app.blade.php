<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'SIN BECA NO HAY RENTA')</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background:#f7f8fa; }
    .navbar-brand { font-weight:700; }
    .container-narrow { max-width: 1120px; }
  </style>
</head>
<body>
@php /** @var \App\Models\User|null $user */ $user = auth()->user(); @endphp

<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
  <div class="container container-narrow">
    <a class="navbar-brand text-primary" href="{{ route('home') }}">
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

  @yield('content')
</main>

<footer class="py-4 border-top bg-white">
  <div class="container container-narrow text-center text-muted small">
    © {{ date('Y') }} Sin beca no hay renta
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
