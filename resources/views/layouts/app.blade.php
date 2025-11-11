<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'SIN BECA NO HAY RENTA')</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Font Awesome (para los íconos del header nuevo) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  {{-- Tailwind para el nuevo header --}}
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body { background:#f7f8fa; }
    .container-narrow { max-width: 1120px; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
    {{-- HEADER GLOBAL --}}
    <x-main-header />

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

    {{-- ===================== MODAL DE LOGIN (Bootstrap) ===================== --}}
    @guest
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fa-regular fa-user me-2"></i> Iniciar sesión
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label for="login-email" class="form-label">Email</label>
                <input id="login-email"
                       type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="username"
                       autofocus>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="login-password" class="form-label">Contraseña</label>
                <input id="login-password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="current-password">
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="remember_me" name="remember">
                  <label class="form-check-label" for="remember_me">Recuérdame</label>
                </div>

                @if (Route::has('password.request'))
                  <a class="small" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                @endif
              </div>
            </div>

            <div class="modal-footer d-block">
              <button type="submit" class="btn btn-primary w-100">
                Entrar
              </button>

              @if (Route::has('register'))
                <p class="text-center mt-3 mb-0 small">
                  ¿No tienes cuenta?
                  <a href="{{ route('register') }}">Regístrate aquí</a>
                </p>
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
    @endguest
    {{-- =================== FIN MODAL LOGIN =================== --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @guest
    <script>
      // Función global para abrir el modal desde cualquier parte (openLoginModal())
      function openLoginModal() {
        const modalEl = document.getElementById('loginModal');
        if (!modalEl || typeof bootstrap === 'undefined') return;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }

      // Si hay errores de validación al intentar iniciar sesión, abre el modal automáticamente
      (function () {
        const hasAuthErrors = {!! ($errors->has('email') || $errors->has('password')) ? 'true' : 'false' !!};
        if (hasAuthErrors) {
          const modalEl = document.getElementById('loginModal');
          if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
          }
        }
      })();
    </script>
    @endguest

    @stack('scripts')
</body>
</html>
