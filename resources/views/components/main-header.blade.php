<header class="sticky top-0 bg-white shadow-sm z-50">
  {{-- Barra superior: logo + acciones móviles --}}
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold text-blue-600 flex items-center gap-2">
      <i class="fa-solid fa-house"></i>
      <span>Sin beca <span class="text-gray-800">no hay renta</span></span>
    </a>

    {{-- Acciones móviles (solo <md) --}}
    <div class="flex items-center gap-3 md:hidden">
      {{-- Campanita móvil (solo agent/admin) --}}
      @auth
        @if(in_array(auth()->user()->role, ['agent','admin']))
          <div class="relative" id="header-notifications-mobile">
            <button id="notifyBtnMobile"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100"
                    aria-label="Notificaciones">
              <i class="fa-regular fa-bell text-lg"></i>
              @if(($unreadNotificationCount ?? 0) > 0)
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 text-[10px] leading-[18px] text-white bg-red-500 rounded-full text-center">
                  {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                </span>
              @endif
            </button>

            @if(isset($headerNotifications))
              <div id="notifyMenuMobile"
                   class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                  <h4 class="text-sm font-semibold text-gray-700">Notificaciones</h4>
                  <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-500">
                      Marcar todas como leídas
                    </button>
                  </form>
                </div>

                <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                  @forelse($headerNotifications as $n)
                    <a href="{{ route('notifications.redirect', $n['id']) }}"
                       class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition">
                      <span class="text-indigo-500 text-lg">
                        <i class="{{ $n['icon'] }}"></i>
                      </span>
                      <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $n['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $n['description'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $n['time'] }}</p>
                      </div>
                      @if(!$n['read'])
                        <span class="mt-1 h-2 w-2 rounded-full bg-blue-500"></span>
                      @endif
                    </a>
                  @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-500">Sin notificaciones por ahora.</div>
                  @endforelse
                </div>
              </div>
            @endif
          </div>
        @endif
      @endauth

      {{-- Lupa móvil --}}
      <button id="search-toggle" class="text-gray-700 text-xl focus:outline-none" aria-label="Abrir filtros">
        <i class="fas fa-search"></i>
      </button>

      {{-- Hamburguesa móvil --}}
      <button id="menu-toggle" class="text-gray-700 text-2xl focus:outline-none" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    {{-- Navegación DESKTOP (md+) --}}
    <nav class="hidden md:flex items-center gap-6 text-gray-700 font-medium">
      {{-- Campanita desktop (solo agent/admin) --}}
      @auth
        @if(in_array(auth()->user()->role, ['agent','admin']))
          <div class="relative" id="header-notifications-desktop">
            <button id="notifyBtnDesktop"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100"
                    aria-label="Notificaciones">
              <i class="fa-regular fa-bell text-lg"></i>
              @if(($unreadNotificationCount ?? 0) > 0)
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 text-[10px] leading-[18px] text-white bg-red-500 rounded-full text-center">
                  {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                </span>
              @endif
            </button>

            @if(isset($headerNotifications))
              <div id="notifyMenuDesktop"
                   class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                  <h4 class="text-sm font-semibold text-gray-700">Notificaciones</h4>
                  <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-500">
                      Marcar todas como leídas
                    </button>
                  </form>
                </div>
                <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                  @forelse($headerNotifications as $n)
                    <a href="{{ route('notifications.redirect', $n['id']) }}"
                       class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition">
                      <span class="text-indigo-500 text-lg"><i class="{{ $n['icon'] }}"></i></span>
                      <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800">{{ $n['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $n['description'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">{{ $n['time'] }}</p>
                      </div>
                      @if(!$n['read'])
                        <span class="mt-1 h-2 w-2 rounded-full bg-blue-500"></span>
                      @endif
                    </a>
                  @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-500">Sin notificaciones por ahora.</div>
                  @endforelse
                </div>
              </div>
            @endif
          </div>
        @endif
      @endauth

      {{-- Links según estado/rol (desktop) --}}
      @guest
        <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
        <button type="button" onclick="handleHeaderLoginClick()"
                class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
          <i class="fa-regular fa-user"></i><span>Iniciar sesión</span>
        </button>
      @endguest

      @auth
        @php($role = auth()->user()->role)

        @if(!in_array($role, ['agent','admin']))
          <a href="{{ route('visits.my') }}" class="hover:text-blue-600 transition">Mis Reservas</a>
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('chat.index') }}" class="hover:text-blue-600 transition">Mis Mensajes</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Vuélvete Agente</a>
        @endif

        @if($role === 'agent')
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('agent.home') }}" class="hover:text-blue-600 transition">Panel de Agente</a>
        @endif

        @if($role === 'admin')
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('admin.index') }}" class="hover:text-blue-600 transition">Panel Administrador</a>
        @endif

        {{-- Dropdown Perfil (desktop) --}}
        <div class="relative" id="header-profile">
          <button id="profileBtn"
                  class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
            <i class="fa-regular fa-user"></i><span>Perfil</span>
            <i class="fa-solid fa-chevron-down text-xs opacity-90"></i>
          </button>
          <div id="profileMenu"
               class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1">
            <a href="{{route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ver Perfil</a>
            <a href="{{route('preferences.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Editar Preferencias</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Cerrar sesión</button>
            </form>
          </div>
        </div>
      @endauth
    </nav>
  </div>

  {{-- Navegación MÓVIL (overlay lateral) --}}
  <nav id="main-nav"
       class="hidden md:hidden flex-col fixed top-0 right-0 h-full w-3/4 bg-white shadow-lg p-6 space-y-4 text-gray-700 font-medium transition-transform transform translate-x-full z-40">
    <button id="close-menu" class="text-gray-500 text-2xl self-end mb-2" aria-label="Cerrar menú">
      <i class="fas fa-times"></i>
    </button>

    @guest
      <a href="{{ route('properties.map') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Mapa</a>
      <button type="button" onclick="handleHeaderLoginClick()"
              class="block bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition text-center">
        <i class="fa-regular fa-user mr-1"></i> Iniciar sesión
      </button>
    @endguest

    @auth
      @php($role = auth()->user()->role)
      @if(!in_array($role, ['agent','admin']))
        <a href="{{ route('visits.my') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Mis Reservas</a>
        <a href="{{ route('favorites.index') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Favoritos</a>
        <a href="{{ route('properties.map') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Mapa</a>
        <a href="{{ route('agent.view') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Vuélvete Agente</a>
      @endif
      @if($role === 'agent')
        <a href="{{ route('favorites.index') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Favoritos</a>
        <a href="{{ route('properties.map') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Mapa</a>
        <a href="{{ route('agent.home') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Panel de Agente</a>
      @endif
      @if($role === 'admin')
        <a href="{{ route('favorites.index') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Favoritos</a>
        <a href="{{ route('properties.map') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Mapa</a>
        <a href="{{ route('admin.index') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600 transition">Panel Administrador</a>
      @endif
      <a href="{{ route('profile.edit') }}" class="block bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition text-center">Perfil</a>
      <form method="POST" action="{{ route('logout') }}">@csrf
        <button type="submit" class="w-full mt-2 text-left px-4 py-2 rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">
          Cerrar sesión
        </button>
      </form>
    @endauth
  </nav>

  @guest
  {{-- Modal de login (fallback) --}}
  <div id="header-login-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative">
      <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
              onclick="window.handleHeaderCloseLogin && window.handleHeaderCloseLogin()">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-regular fa-user"></i><span>Iniciar sesión</span>
      </h2>
      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
          <label for="header-login-email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="header-login-email" type="email" name="email" required autocomplete="username"
                 class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div>
          <label for="header-login-password" class="block text-sm font-medium text-gray-700">Contraseña</label>
          <input id="header-login-password" type="password" name="password" required autocomplete="current-password"
                 class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 text-gray-700">
            <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
            <span>Recuérdame</span>
          </label>
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
          @endif
        </div>
        <button type="submit"
                class="w-full inline-flex justify-center items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
          Entrar
        </button>
        @if (Route::has('register'))
          <p class="mt-3 text-center text-xs text-gray-500">¿Aún no tienes cuenta?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Regístrate</a>
          </p>
        @endif
      </form>
    </div>
  </div>
  @endguest

  {{-- JS --}}
  <script>
    (function () {
      // Utilidad: inicializar dropdown de notificaciones con IDs únicos
      function setupNotify(wrapId, btnId, menuId) {
        const wrap = document.getElementById(wrapId);
        if (!wrap) return;
        const btn = document.getElementById(btnId);
        const menu = document.getElementById(menuId);
        if (!btn || !menu) return;

        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          menu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
          if (!wrap.contains(e.target)) menu.classList.add('hidden');
        });
      }

      // Login (usa modal global si existe; si no, el del header)
      window.handleHeaderLoginClick = function () {
        if (typeof openLoginModal === 'function') { openLoginModal(); return; }
        const modal = document.getElementById('header-login-modal');
        if (modal) modal.classList.remove('hidden');
      };
      window.handleHeaderCloseLogin = function () {
        const modal = document.getElementById('header-login-modal');
        if (modal) modal.classList.add('hidden');
      };
      const hLoginModal = document.getElementById('header-login-modal');
      if (hLoginModal) {
        hLoginModal.addEventListener('click', (e) => { if (e.target === hLoginModal) window.handleHeaderCloseLogin(); });
      }

      // Perfil (desktop)
      const pWrap = document.getElementById('header-profile');
      if (pWrap) {
        const pBtn  = document.getElementById('profileBtn');
        const pMenu = document.getElementById('profileMenu');
        pBtn.addEventListener('click', (e) => { e.stopPropagation(); pMenu.classList.toggle('hidden'); });
        document.addEventListener('click', (e) => { if (!pWrap.contains(e.target)) pMenu.classList.add('hidden'); });
      }

      // Notificaciones: inicializa móvil y desktop por separado
      setupNotify('header-notifications-mobile',  'notifyBtnMobile',  'notifyMenuMobile');
      setupNotify('header-notifications-desktop', 'notifyBtnDesktop', 'notifyMenuDesktop');

      // Menú móvil (overlay)
      const menuToggle = document.getElementById('menu-toggle');
      const closeMenu  = document.getElementById('close-menu');
      const mainNav    = document.getElementById('main-nav');
      if (menuToggle && closeMenu && mainNav) {
        menuToggle.addEventListener('click', () => {
          mainNav.classList.remove('translate-x-full', 'hidden');
        });
        closeMenu.addEventListener('click', () => {
          mainNav.classList.add('translate-x-full');
          setTimeout(() => mainNav.classList.add('hidden'), 300);
        });
      }
    })();
  </script>
</header>
