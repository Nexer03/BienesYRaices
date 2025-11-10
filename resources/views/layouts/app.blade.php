<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Sin beca no hay renta')</title>

  {{-- Bootstrap + Icons + FontAwesome --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

  <style>
    :root{
      --brand-blue:#2f6df6;           /* tono del botón Perfil */
      --nav-text:#2b3643;              /* gris azulado de los links */
      --footer-bg:#1f2937;             /* gris oscuro del footer */
      --footer-muted:#cbd5e1;          /* texto claro */
    }
    body{ background:#f7f8fa; color:#111827; }
    .navbar { background:#fff; box-shadow:0 1px 0 rgba(17,24,39,.06); }
    .navbar .nav-link{ color:var(--nav-text); font-weight:600; }
    .navbar .nav-link:hover{ color:#1f5bd6; }
    .btn-pill{
      border-radius:9999px;
      padding:.5rem 1.1rem;
      font-weight:700;
      background:var(--brand-blue);
      color:#fff;
      border:none;
    }
    .btn-pill:hover{ background:#2a5fe0; color:#fff; }
    .notif-badge{
      position:absolute; top:-6px; right:-6px;
      background:#ef4444; color:#fff; font-size:.65rem;
      border-radius:9999px; padding:.05rem .35rem; font-weight:700;
      line-height:1.1;
    }
    /* Footer */
    .footer-dark{ background:var(--footer-bg); color:var(--footer-muted); }
    .footer-dark a{ color:var(--footer-muted); text-decoration:none; }
    .footer-dark a:hover{ color:#fff; }
    .footer-divider{ border-top:1px solid rgba(255,255,255,.12); }

    /* Ajuste visual exacto del logotipo */
.navbar-brand {
  font-size: 1.6rem !important;     /* más grande */
  font-weight: 700 !important;      /* negrita sólida */
  letter-spacing: -0.3px;
}

.navbar-brand i {
  font-size: 1.8rem !important;     /* ícono proporcional */
  margin-right: 0.45rem !important;
  color: #2563eb !important;        /* azul igual al original */
}

.navbar-brand span.text-dark {
  color: #1e293b !important;        /* gris oscuro limpio */
}

  </style>
</head>
<body class="d-flex flex-column min-vh-100">

{{-- ======================== HEADER GLOBAL ======================== --}}
<nav class="navbar navbar-expand-lg sticky-top py-3">
  <div class="container-lg">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
      <i class="bi bi-house-door-fill me-2"></i>
      <span>Sin beca <span class="text-dark">no hay renta</span></span>
    </a>

    {{-- Acciones a la derecha (campana + offcanvas toggle) --}}
    <div class="d-flex align-items-center gap-3 order-lg-2">
      @auth
        @if(in_array(auth()->user()->role, ['agent','admin']))
        <div class="dropdown">
          <div class="dropdown-menu dropdown-menu-end p-0 shadow">
            <div class="border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
              <strong class="small text-muted">Notificaciones</strong>
              <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button class="btn btn-link btn-sm p-0 text-primary">Marcar todas como leídas</button>
              </form>
            </div>
            <div style="max-height:20rem; overflow:auto;">
              @forelse(($headerNotifications ?? collect()) as $n)
                <a class="dropdown-item d-flex gap-2 py-2" href="{{ route('notifications.redirect', $n['id']) }}">
                  <span class="text-primary"><i class="{{ $n['icon'] }}"></i></span>
                  <div class="small">
                    <div class="fw-semibold text-dark">{{ $n['title'] }}</div>
                    <div class="text-muted">{{ $n['description'] }}</div>
                    <div class="text-secondary" style="font-size:.7rem">{{ $n['time'] }}</div>
                  </div>
                  @unless($n['read'])
                    <span class="ms-auto mt-1 d-inline-block" style="width:8px;height:8px;border-radius:50%;background:#3b82f6"></span>
                  @endunless
                </a>
              @empty
                <div class="px-3 py-4 text-center text-muted small">Sin notificaciones por ahora.</div>
              @endforelse
            </div>
          </div>
        </div>
        @endif
      @endauth

      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    {{-- Menú (desktop) --}}
    <div class="collapse navbar-collapse order-lg-1">
<ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">

  {{-- Panel principal --}}
  <li class="nav-item">
    <a class="nav-link" href="{{ route('dashboard') }}">Panel principal</a>
  </li>

  {{-- Propiedades --}}
  <li class="nav-item">
    <a class="nav-link" href="{{ route('properties.index') }}">Propiedades</a>
  </li>

  {{-- Agenda --}}
  <li class="nav-item">
    <a class="nav-link" href="{{ route('visits.my') }}">Agenda</a>
  </li>

  {{-- Solo administradores --}}
  @if(auth()->check() && auth()->user()->role === 'admin')
  <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.users.index') }}">Usuarios</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.reports.index') }}">Reportes</a>
  </li>
  @endif

  {{-- Perfil --}}
  @auth
    <li class="nav-item">
      <a class="btn btn-pill" href="{{ route('profile.edit') }}">Perfil</a>
    </li>
  @endauth
</ul>


    </div>
  </div>
</nav>

{{-- Menú (móvil) Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="mainMenu">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Menú</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>
  <div class="offcanvas-body">
    <div class="list-group list-group-flush">
      <a class="list-group-item list-group-item-action" href="{{ route('visits.my') }}">Mi agenda</a>
      @auth
        <a class="list-group-item list-group-item-action" href="{{ route('favorites.index') }}">Favoritos</a>
      @endauth
      <a class="list-group-item list-group-item-action" href="{{ route('properties.map') }}">Mapa</a>
      @auth
        @if(auth()->user()->role === 'agent')
          <a class="list-group-item list-group-item-action" href="{{ route('agent.home') }}">Panel de Agente</a>
        @else
          <a class="list-group-item list-group-item-action" href="{{ route('agent.view') }}">Modo vendedor</a>
        @endif
        <a class="btn btn-pill mt-3" href="{{ url('/dashboard') }}">Perfil</a>
      @else
        <a class="list-group-item list-group-item-action" href="{{ route('agent.view') }}">Modo vendedor</a>
        <button class="btn btn-pill mt-3" onclick="openLoginModal()">Iniciar sesión</button>
      @endauth
    </div>
  </div>
</div>
{{-- ====================== /HEADER GLOBAL ====================== --}}

<main class="flex-grow-1">
  @yield('content')
</main>

{{-- ======================== FOOTER GLOBAL ======================== --}}
<footer class="footer-dark mt-auto pt-5">
  <div class="container-lg pb-4">
    <div class="row g-4">
      <div class="col-12 col-lg-6">
        <div class="d-flex align-items-center text-white fw-bold fs-5 mb-3">
          <i class="bi bi-house-door-fill me-2"></i> SIN BECA NO HAY RENTA
        </div>
        <p class="mb-3">
          Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades
          con sus futuros dueños de manera eficiente y profesional.
        </p>
        <div class="d-flex gap-3 fs-5">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3">
        <h6 class="text-white mb-3">Navegación</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="{{ route('visits.my') }}">Mis Visitas</a></li>
          <li class="mb-2"><a href="{{ route('properties.map') }}">Mapa</a></li>
          <li class="mb-2"><a href="{{ route('agent.home') }}">Panel de Agente</a></li>
          <li class="mb-2"><a href="{{ route('agent.view') }}">Modo Vendedor</a></li>
        </ul>
      </div>

      <div class="col-12 col-md-4 col-lg-3">
        <h6 class="text-white mb-3">Contacto</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><i class="bi bi-envelope me-2"></i>soporte@sinbeca.com</li>
          <li class="mb-2"><i class="bi bi-telephone me-2"></i>+1 (555) 123-4567</li>
          <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Ciudad, País</li>
        </ul>
      </div>
    </div>

    <div class="footer-divider my-4"></div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pb-4 small">
      <div>© {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</div>
      <div class="d-flex gap-4 mt-3 mt-md-0">
        <a href="#">Privacidad</a>
        <a href="#">Términos</a>
        <a href="#">Cookies</a>
      </div>
    </div>
  </div>
</footer>
{{-- ====================== /FOOTER GLOBAL ====================== --}}

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
