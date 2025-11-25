<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mapa de Propiedades - SIN BECA NO HAY RENTA</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Anti-flash -->
  <script>
    (function () {
      try {
        if (localStorage.getItem('theme') === 'dark') {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
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

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" rel="stylesheet">

  <style>
    /* ===== Transición global de tema ===== */
    html.theme-fade * {
      transition: background-color .35s ease, color .35s ease, border-color .35s ease, fill .35s ease, box-shadow .35s ease;
    }

    /* ===== MAPA ===== */
    #map { height: 80vh; width: 100%; border-radius: 1rem; }

    /* ===== Marcadores de precio (usan variables para tema) ===== */
    :root{
      --marker-bg: #ffffff;
      --marker-fg: #111827;
      --marker-border: #d1d5db;
      --marker-shadow: 0 2px 6px rgba(0,0,0,.15);
    }
    html.dark{
      --marker-bg: rgba(17,24,39,.88); /* slate-900 con transparencia */
      --marker-fg: #F9FAFB;            /* gray-50 */
      --marker-border: #0f172a;        /* slate-900 */
      --marker-shadow: 0 10px 24px rgba(0,0,0,.55);
    }
    .price-marker{
      position: relative;
      background: var(--marker-bg);
      color: var(--marker-fg);
      border: 1px solid var(--marker-border);
      border-radius: 9999px;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .2px;
      box-shadow: var(--marker-shadow);
      backdrop-filter: saturate(140%) blur(2px);
      cursor: pointer;
      transition: transform .2s ease, background-color .35s ease, color .35s ease, border-color .35s ease, box-shadow .35s ease;
    }
    .price-marker:hover{ transform: translateY(-1px) scale(1.05); }
    .price-marker::after{
      content:'';
      position:absolute; inset:-3px;
      border-radius: 9999px;
      box-shadow: 0 0 0 1px rgba(255,255,255,.06) inset;
      pointer-events:none;
    }

    /* 👇 Fuerza texto blanco en oscuro (más específico + !important) */
    html.dark .price-marker{
      color:#ffffff !important;
      background: rgba(17,24,39,.9);
      border-color:#334155;
      box-shadow: 0 10px 24px rgba(0,0,0,.55);
      text-shadow: 0 1px 2px rgba(0,0,0,.6);
    }

    /* ===== Modal ===== */
    .modal{ display:flex; justify-content:center; align-items:center; position:fixed; inset:0; background:rgba(0,0,0,.55); transition: opacity .22s ease; z-index:60; }
    .modal.hidden{ opacity:0; pointer-events:none; }
    .modal-content{
      position:relative; background:#fff; color:#0f172a;
      padding:1.5rem 1.75rem; border-radius:1.25rem; max-width:720px; width:100%;
      transform: translateY(8px) scale(.97); transition: transform .22s ease;
      box-shadow: 0 20px 45px rgba(15,23,42,.30);
    }
    .modal:not(.hidden) .modal-content{ transform: translateY(0) scale(1); }
    html.dark .modal-content{ background:#020617; color:#e5e7eb; }

    .carousel-container{ overflow:hidden; border-radius:1rem; background:#f3f4f6; aspect-ratio:4/3; display:flex; align-items:center; justify-content:center; }
    html.dark .carousel-container{ background:#0b1220; }
    .carousel-item{ width:100%; height:100%; object-fit:cover; border-radius:inherit; }

    /* ===== Panel de filtros (mejor spot) ===== */
    #map-filter-panel{
      z-index:50;
      width: 20rem; /* 320px */
    }
    /* Ocultar POIs ruidosos */
    .gm-style img[src*="spotlight-poi"],
    .gm-style div[style*="background-image"]{ display:none!important; }
  </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-300">
  <x-main-header />

  <!-- Botón tema -->
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
    <i id="theme-toggle-icon" class="fa-solid"></i>
    <span class="text-sm font-medium"></span>
  </button>

  <main class="relative flex-grow">
    <!-- Panel de filtros: top más bajo y más cercano al borde -->
    <aside id="map-filter-panel"
           class="fixed top-28 left-6 md:left-8 lg:left-10">
      <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md shadow-2xl rounded-2xl p-4 space-y-4 border border-gray-100 dark:border-gray-800 transition-all duration-300">
        <div class="flex items-center justify-between mb-1">
          <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
            <i class="fa-solid fa-filter text-blue-500"></i>
            <span>Filtros del mapa</span>
          </h2>
        </div>

        <!-- Inputs ocultos para compat -->
        <input type="number" id="minPrice" class="hidden" />
        <input type="number" id="maxPrice" class="hidden" />

        <!-- Filtro de precio -->
        <div class="space-y-2">
          <span class="text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">Precio</span>
          <div class="relative w-full">
            <button type="button" id="price-filter-button"
                    class="w-full text-left border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-gray-100">
              <span>Precio</span>
            </button>

            <div id="price-dropdown"
                 class="hidden absolute top-full mt-2 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 p-4">
              <p class="font-semibold text-gray-800 dark:text-gray-100 mb-3 text-sm">Rango de Precio</p>
              <div id="price-slider" class="mb-4 mx-1"></div>
              <div class="flex justify-between items-center text-xs text-gray-700 dark:text-gray-200">
                <div class="flex items-center gap-1 border rounded-md px-2 py-1 bg-gray-50 dark:bg-gray-800">$ <span id="slider-min-value"></span></div>
                <div class="text-gray-400">-</div>
                <div class="flex items-center gap-1 border rounded-md px-2 py-1 bg-gray-50 dark:bg-gray-800">$ <span id="slider-max-value"></span></div>
              </div>
              <input type="hidden" id="slider-min-input"><input type="hidden" id="slider-max-input">
              <div class="mt-4 text-right">
                <button type="button" id="apply-price-button"
                        class="bg-blue-600 text-white px-4 py-1.5 rounded-full text-xs font-semibold hover:bg-blue-700 transition">
                  Aplicar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tipo -->
        <div class="space-y-1">
          <label for="listingType" class="block text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wide">Tipo de propiedad</label>
          <select id="listingType"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-gray-100">
            <option value="rent">Renta</option>
            <option value="sale">Venta</option>
          </select>
        </div>

        <button id="filterBtn"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition text-sm font-semibold flex items-center justify-center gap-2 shadow">
          <i class="fa-solid fa-magnifying-glass"></i>
          <span>Aplicar filtros</span>
        </button>
      </div>
    </aside>

    <!-- Map -->
    <section class="w-full h-full max-w-7xl mx-auto px-4 md:px-6 pt-4 pb-8">
      <div id="map" class="shadow-md bg-gray-200 dark:bg-gray-900 transition-colors duration-300"></div>
    </section>
  </main>

  <!-- Modal -->
  <div id="propertyModal" class="modal hidden">
    <div class="modal-content">
      <button onclick="closePropertyModal()" class="absolute right-3 top-3 text-gray-400 hover:text-gray-200">
        <i class="fas fa-times text-sm"></i>
      </button>

      <button type="button"
              id="favoriteBtn"
              class="absolute right-12 top-3 bg-white/95 dark:bg-gray-800/95 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700"
              aria-label="Guardar en favoritos">
        <i class="fa-regular fa-heart text-gray-700 dark:text-gray-100 text-sm"></i>
      </button>

      <div class="flex flex-col md:flex-row gap-6 mt-4 md:mt-2">
        <div class="w-full md:w-1/2">
          <div id="propertyCarousel" class="carousel-container"></div>
        </div>
        <div id="modalInfo" class="w-full md:w-1/2 flex flex-col justify-between"></div>
      </div>
    </div>
  </div>

  <x-main-footer />

  <!-- noUiSlider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

  <!-- Google Maps (con key del backend) -->
  <script>
    // Datos del back
    const properties = @json($properties);
    let favoriteIds = @json($favoriteIds ?? []);
    const favoriteToggleUrls = @json($favoriteToggleUrls ?? []);
    const loginUrl = @json($loginUrl ?? route('login'));
    const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';

    // Estilos de mapa (claro/oscuro)
    const lightMapStyle = [
      {featureType:"poi",stylers:[{visibility:"off"}]},
      {featureType:"transit",stylers:[{visibility:"off"}]},
      {featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]}
    ];
    const darkMapStyle = [
      {elementType:"geometry",stylers:[{color:"#1f2937"}]},
      {elementType:"labels.text.fill",stylers:[{color:"#93a4b8"}]},
      {elementType:"labels.text.stroke",stylers:[{color:"#1f2937"}]},
      {featureType:"administrative",elementType:"geometry",stylers:[{color:"#334155"}]},
      {featureType:"poi",stylers:[{visibility:"off"}]},
      {featureType:"road",elementType:"labels.icon",stylers:[{visibility:"off"}]},
      {featureType:"road",elementType:"geometry",stylers:[{color:"#2b3647"}]},
      {featureType:"road",elementType:"geometry.stroke",stylers:[{color:"#374151"}]},
      {featureType:"water",elementType:"geometry",stylers:[{color:"#0b1220"}]},
      {featureType:"transit",stylers:[{visibility:"off"}]}
    ];

    let map, markers = [];

    function closePropertyModal(){
      const modal = document.getElementById('propertyModal');
      const carousel = document.getElementById('propertyCarousel');
      const modalInfo = document.getElementById('modalInfo');
      if(modal) modal.classList.add('hidden');
      if(carousel) carousel.innerHTML = '';
      if(modalInfo) modalInfo.innerHTML = '';
    }

    // Cargar Maps
    fetch('/maps-key')
      .then(res => res.json())
      .then(data => {
        const s = document.createElement('script');
        s.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
        s.async = true;
        document.head.appendChild(s);
      });

    // Init map
    function initMap(){
      const defaultLocation = { lat: 20.749757, lng: -105.258849 };
      const isDark = document.documentElement.classList.contains('dark');

      map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 12,
        styles: isDark ? darkMapStyle : lightMapStyle,
        mapTypeControl: false,
        streetViewControl: true
      });

      // Marcadores
      properties.forEach(p=>{
        if(!p.latitude || !p.longitude) return;
        const m = new google.maps.Marker({
          position: {lat: parseFloat(p.latitude), lng: parseFloat(p.longitude)},
          map,
          label: {
            text: `$${Number(p.price).toLocaleString('es-MX')}`,
            className: 'price-marker'
          },
          icon: ' ',
          title: p.title,
          listingType: p.listing_type,
          price: p.price
        });
        m.addListener('click', ()=>openPropertyModal(p));
        markers.push(m);
      });

      // Filtros
      document.getElementById('filterBtn')?.addEventListener('click', filterMarkers);
      filterMarkers();
    }

    function filterMarkers(){
      const min = Math.max(0, parseFloat(document.getElementById('minPrice').value)||0);
      const max = Math.max(0, parseFloat(document.getElementById('maxPrice').value)||Infinity);
      const type = document.getElementById('listingType').value;

      markers.forEach(m=>{
        const okPrice = m.price >= min && m.price <= max;
        const okType  = !type || m.listingType === type;
        m.setMap(okPrice && okType ? map : null);
      });
    }

    function setFavoriteButtonState(btn, isOn){
      if(!btn) return;
      btn.dataset.state = isOn ? 'on' : 'off';
      const icon = btn.querySelector('i');
      if(icon){
        icon.classList.remove('fa-solid','text-red-500');
        icon.classList.remove('fa-regular','text-gray-700','dark:text-gray-100');
        if(isOn){
          icon.classList.add('fa-solid','text-red-500');
        }else{
          icon.classList.add('fa-regular','text-gray-700','dark:text-gray-100');
        }
      }
    }

    function updateFavoriteIds(propId, isFav){
      const numericId = Number(propId);
      if(isFav){
        if(!favoriteIds.includes(numericId)) favoriteIds.push(numericId);
      }else{
        favoriteIds = favoriteIds.filter(id => id !== numericId);
      }
    }

    async function handleFavoriteToggle(ev){
      ev.preventDefault();
      ev.stopPropagation();
      const btn = ev.currentTarget;
      const toggleUrl = btn.dataset.toggleUrl;
      const loginRedirect = btn.dataset.loginUrl;

      if(!toggleUrl){
        if(loginRedirect) window.location.href = loginRedirect;
        return;
      }

      try{
        const res = await fetch(toggleUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        });

        if(!res.ok){
          if(loginRedirect) window.location.href = loginRedirect;
          return;
        }

        const data = await res.json();
        if(!data || data.ok !== true){
          if(loginRedirect) window.location.href = loginRedirect;
          return;
        }

        const isFav = data.favorited === true;
        updateFavoriteIds(btn.dataset.propertyId, isFav);
        setFavoriteButtonState(btn, isFav);
      }catch(e){
        if(loginRedirect) window.location.href = loginRedirect;
      }
    }

    function openPropertyModal(prop){
      const modal = document.getElementById('propertyModal');
      const carousel = document.getElementById('propertyCarousel');
      const info = document.getElementById('modalInfo');
      const favoriteBtn = document.getElementById('favoriteBtn');
      if(!modal || !carousel || !info) return;

      carousel.innerHTML = ''; info.innerHTML = '';

      const typeLabel = prop.listing_type === 'sale' ? 'En venta' : 'En renta';
      const locationText = prop.location ?? '';

      info.innerHTML = `
        <div class="space-y-2">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700">${typeLabel}</span>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">${prop.title}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">${locationText}</p>
        </div>
        <div class="mt-4 space-y-3">
          <p class="text-2xl font-bold text-blue-600">$${Number(prop.price).toLocaleString('es-MX')}</p>
          <a href="/properties/${prop.id}" class="inline-flex items-center px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
            Ver detalles <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
          </a>
        </div>
      `;

      const imgs = prop.images && prop.images.length ? prop.images : [];
      const mainSrc = imgs.length ? `{{ asset('storage') }}/${imgs[0].image_path}` : 'https://via.placeholder.com/400x300?text=Sin+Imagen';
      const extra = Math.max(0, imgs.length - 1);
      carousel.innerHTML = `
        <div class="relative w-full h-full">
          <img src="${mainSrc}" alt="${prop.title}" class="carousel-item"/>
          ${ extra ? `<span class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] px-2 py-1 rounded-full">+${extra} fotos</span>` : '' }
        </div>
      `;
      if(favoriteBtn){
        const isFav = favoriteIds.includes(prop.id);
        favoriteBtn.dataset.propertyId = prop.id;
        favoriteBtn.dataset.loginUrl = isAuthenticated ? '' : loginUrl;
        favoriteBtn.dataset.toggleUrl = isAuthenticated ? (favoriteToggleUrls[String(prop.id)] || '') : '';
        setFavoriteButtonState(favoriteBtn, isFav);
      }
      modal.classList.remove('hidden');
    }

    /* ====== Slider precio + dropdown ====== */
    document.addEventListener('DOMContentLoaded', function () {
      const priceSlider = document.getElementById('price-slider');
      const minDisplay  = document.getElementById('slider-min-value');
      const maxDisplay  = document.getElementById('slider-max-value');
      const minInput    = document.getElementById('slider-min-input');
      const maxInput    = document.getElementById('slider-max-input');
      const minCompat   = document.getElementById('minPrice');
      const maxCompat   = document.getElementById('maxPrice');
      const priceBtn    = document.getElementById('price-filter-button');
      const priceDrop   = document.getElementById('price-dropdown');
      const applyBtn    = document.getElementById('apply-price-button');
      const typeSelect  = document.getElementById('listingType');
      const favoriteBtn = document.getElementById('favoriteBtn');

      const RANGES = { rent: {min:100, max:10000, step:100}, sale: {min:500000, max:10000000, step:50000} };
      const fmt = new Intl.NumberFormat('es-MX', {style:'decimal', maximumFractionDigits:0});

      const rangeForType = () => (typeSelect.value === 'sale' ? RANGES.sale : RANGES.rent);

      function setBtnText(min, max, cfg){
        priceBtn.innerHTML = (min>cfg.min || max<cfg.max) ? `<span>$${fmt.format(min)} - $${fmt.format(max)}</span>` : `<span>Precio</span>`;
      }

      function init(){
        if(!priceSlider) return;
        const cfg = rangeForType();
        let a = parseInt(minCompat.value || cfg.min, 10);
        let b = parseInt(maxCompat.value || cfg.max, 10);
        a = Math.max(cfg.min, Math.min(a, cfg.max));
        b = Math.max(cfg.min, Math.min(b, cfg.max));
        if(a>b) a=b;

        if(priceSlider.noUiSlider){
          priceSlider.noUiSlider.updateOptions({start:[a,b], step:cfg.step, range:{min:cfg.min,max:cfg.max}}, true);
        }else{
          noUiSlider.create(priceSlider, {start:[a,b], connect:true, step:cfg.step, range:{min:cfg.min,max:cfg.max}});
        }
        minDisplay.textContent = fmt.format(a); maxDisplay.textContent = fmt.format(b);
        minInput.value=a; maxInput.value=b; minCompat.value=a; maxCompat.value=b; setBtnText(a,b,cfg);
        priceSlider.noUiSlider.off?.('update');
        priceSlider.noUiSlider.on('update', v=>{
          const x = Math.round(v[0]), y = Math.round(v[1]);
          minDisplay.textContent = fmt.format(x); maxDisplay.textContent = fmt.format(y);
          minInput.value=x; maxInput.value=y; minCompat.value=x; maxCompat.value=y; setBtnText(x,y,cfg);
        });
      }

      priceBtn?.addEventListener('click', e=>{ e.stopPropagation(); priceDrop.classList.toggle('hidden'); });
      applyBtn?.addEventListener('click', ()=>{ priceDrop.classList.add('hidden'); filterMarkers(); });
      window.addEventListener('click', e=>{ if(!priceDrop.classList.contains('hidden') && !priceDrop.contains(e.target) && e.target!==priceBtn){ priceDrop.classList.add('hidden'); } });
      typeSelect?.addEventListener('change', ()=>{ const cfg=rangeForType(); minCompat.value=cfg.min; maxCompat.value=cfg.max; init(); filterMarkers(); });
      favoriteBtn?.addEventListener('click', handleFavoriteToggle);

      init();
    });

    /* ===== Botón de tema (incluye transición y cambia estilo del mapa) ===== */
    (function(){
      const html = document.documentElement;
      const btn  = document.getElementById('theme-toggle');
      const icon = document.getElementById('theme-toggle-icon');
      const label= btn?.querySelector('span');

      function setIconAndLabel(){
        const isDark = html.classList.contains('dark');
        icon?.classList.remove('fa-sun','fa-moon');
        icon?.classList.add(isDark ? 'fa-moon' : 'fa-sun');
        if(label) label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
      }
      function startFade(){
        html.classList.add('theme-fade');
        setTimeout(()=>html.classList.remove('theme-fade'), 420);
      }
      function apply(mode){
        const dark = mode==='dark';
        startFade();
        html.classList.toggle('dark', dark);
        try{ localStorage.setItem('theme', mode); }catch(e){}
        setIconAndLabel();
        // Actualiza estilo del mapa sin recrearlo
        if(window.google && map){
          map.setOptions({styles: dark ? darkMapStyle : lightMapStyle});
        }
      }
      setIconAndLabel();
      btn?.addEventListener('click', ()=>{
        const next = html.classList.contains('dark') ? 'light' : 'dark';
        apply(next);
      });
    })();
  </script>
</body>
</html>
