<header class="sticky top-0 bg-white shadow-sm z-50">
  <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-3">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-bold text-blue-600 flex items-center gap-2">
      <i class="fa-solid fa-house"></i>
      <span>Sin beca <span class="text-gray-800">no hay renta</span></span>
    </a>

    {{-- Navegación derecha --}}
    <nav class="flex items-center gap-6 text-gray-700 font-medium">

      {{-- Campanita: sólo para agent/admin --}}
      @auth
        @if(in_array(auth()->user()->role, ['agent','admin']))
          <div class="relative" id="header-notifications">
            <button id="notifyBtn"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100"
                    aria-label="Notificaciones">
              <i class="fa-regular fa-bell text-lg"></i>

              @if(($unreadNotificationCount ?? 0) > 0)
                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 text-[10px] leading-[18px] text-white bg-red-500 rounded-full text-center">
                  {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                </span>
              @endif
            </button>

            {{-- Dropdown opcional (si pasas $headerNotifications) --}}
            @if(isset($headerNotifications))
              <div id="notifyMenu"
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
                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                      Sin notificaciones por ahora.
                    </div>
                  @endforelse
                </div>
              </div>
            @endif
          </div>
        @endif
      @endauth

      {{-- Links según estado/rol --}}
      @guest
        <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>

        <button type="button"
                onclick="openLoginModal()"
                class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
          <i class="fa-regular fa-user"></i>
          <span>Iniciar sesión</span>
        </button>
      @endguest

      @auth
        @php($role = auth()->user()->role)

        {{-- USER (no agent/admin) --}}
        @if(!in_array($role, ['agent','admin']))
          <a href="{{ route('visits.my') }}" class="hover:text-blue-600 transition">Mi agenda</a>
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('agent.view') }}" class="hover:text-blue-600 transition">Vuélvete Agente</a>
        @endif

        {{-- AGENT --}}
        @if($role === 'agent')
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('agent.home') }}" class="hover:text-blue-600 transition">Panel de Agente</a>
        @endif

        {{-- ADMIN --}}
        @if($role === 'admin')
          <a href="{{ route('favorites.index') }}" class="hover:text-blue-600 transition">Favoritos</a>
          <a href="{{ route('properties.map') }}" class="hover:text-blue-600 transition">Mapa</a>
          <a href="{{ route('admin.index') }}" class="hover:text-blue-600 transition">Panel Administrador</a>
        @endif

        {{-- Dropdown Perfil (botón azul) --}}
        <div class="relative" id="header-profile">
          <button id="profileBtn"
                  class="inline-flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">
            <i class="fa-regular fa-user"></i>
            <span>Perfil</span>
            <i class="fa-solid fa-chevron-down text-xs opacity-90"></i>
          </button>

          <div id="profileMenu"
               class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1">
            <a href="{{ route('profile.edit') }}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Ver perfil</a>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                Cerrar sesión
              </button>
            </form>
          </div>
        </div>
      @endauth
    </nav>
  </div>

  {{-- JS del componente (toggle menús) --}}
  <script>
    (function () {
      // Perfil
      const pWrap = document.getElementById('header-profile');
      if (pWrap) {
        const pBtn  = document.getElementById('profileBtn');
        const pMenu = document.getElementById('profileMenu');
        pBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          pMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
          if (!pWrap.contains(e.target)) pMenu.classList.add('hidden');
        });
      }

      // Notificaciones (si existen en la vista)
      const nWrap = document.getElementById('header-notifications');
      if (nWrap) {
        const nBtn  = document.getElementById('notifyBtn');
        const nMenu = document.getElementById('notifyMenu');
        if (nBtn && nMenu) {
          nBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            nMenu.classList.toggle('hidden');
          });
          document.addEventListener('click', (e) => {
            if (!nWrap.contains(e.target)) nMenu.classList.add('hidden');
          });
        }
      }
    })();
  </script>
</header>
