<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $property->title }}</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  <style>
    /* ===== Modal y mapa ===== */
    #reservationModal > div{
      max-width: 720px;
      width:calc(100% - 32px);
      max-height:calc(100vh - 80px);
      overflow:auto;
      border-radius:16px;
      padding:24px;
    }
    #map{ height:400px; width:100%; border-radius:8px; }

    /* Ocultamos el input “host” de flatpickr */
    .fp-hidden-input{
      position:absolute !important; width:1px !important; height:1px !important;
      padding:0 !important; margin:-1px !important; border:0 !important;
      clip:rect(0 0 0 0) !important; overflow:hidden !important;
      white-space:nowrap !important;
    }
    /* Calendario embebido en el shell */
    .fp-shell .flatpickr-calendar{
      position:static !important; border:0 !important; box-shadow:none !important;
      width:100% !important; max-width:100%;
      background:#fff;
    }

    /* ====== LOOK tipo Airbnb (con mayor especificidad) ====== */
    .fp-shell .flatpickr-months{display:flex;background:#fff;padding:10px 0 0;}
    .fp-shell .flatpickr-months .flatpickr-month{flex:1;color:#222;height:50px;line-height:50px;text-align:center;position:relative;}
    .fp-shell .flatpickr-months .flatpickr-prev-month,
    .fp-shell .flatpickr-months .flatpickr-next-month{
      position:absolute;top:50%;width:20px;height:20px;margin-top:-10px;border-radius:9999px;color:#717171;fill:#717171;cursor:pointer;
    }
    .fp-shell .flatpickr-months .flatpickr-prev-month{left:3px;}
    .fp-shell .flatpickr-months .flatpickr-next-month{right:3px;}
    .fp-shell .flatpickr-months .flatpickr-prev-month:hover,
    .fp-shell .flatpickr-months .flatpickr-next-month:hover{color:#222;fill:#222;}
    .fp-shell .flatpickr-months .flatpickr-current-month{font-size:110%;}
    .fp-shell .flatpickr-months .flatpickr-current-month .cur-month{font-weight:700;color:#222;}
    .fp-shell .flatpickr-months .flatpickr-current-month .cur-year{font-weight:400;color:#717171;background:transparent;}

    .fp-shell .flatpickr-weekdays{display:flex;align-items:center;height:28px;margin-bottom:5px}
    .fp-shell .flatpickr-weekdays .flatpickr-weekdaycontainer{flex:1;display:flex}
    .fp-shell span.flatpickr-weekday{flex:1;text-align:center;font-size:11px;color:#717171!important;font-weight:600;text-transform:uppercase}

    .fp-shell .flatpickr-days{width:100%}
    .fp-shell .dayContainer{padding:1px 0 10px;min-width:315px}
    .fp-shell .flatpickr-day{
      color:#222;border:1px solid transparent;background:none;border-radius:50%;
      height:38px;line-height:38px;max-width:38px;flex:0 0 14.2857143%;text-align:center;cursor:pointer;
    }
    .fp-shell .flatpickr-day:hover,
    .fp-shell .flatpickr-day:focus{background:#f7f7f7;border-color:#f7f7f7;outline:0}
    .fp-shell .flatpickr-day.today{border-color:#222;color:#222}
    .fp-shell .flatpickr-day.today:hover{background:#222;border-color:#222;color:#fff}

    .fp-shell .flatpickr-day.selected,
    .fp-shell .flatpickr-day.startRange,
    .fp-shell .flatpickr-day.endRange{background:#222!important;color:#fff!important;border-color:#222!important}
    .fp-shell .flatpickr-day.inRange{background:#f7f7f7!important;border-color:#f7f7f7!important;box-shadow:-5px 0 0 #f7f7f7,5px 0 0 #f7f7f7}
    .fp-shell .flatpickr-day.startRange{border-radius:50% 0 0 50%}
    .fp-shell .flatpickr-day.endRange{border-radius:0 50% 50% 0}
    .fp-shell .flatpickr-day.startRange.endRange{border-radius:50%}

    .fp-shell .flatpickr-day.disabled,
    .fp-shell .flatpickr-day.disabled:hover{color:#d9d9d9!important;background:none!important;border-color:transparent!important;cursor:default;text-decoration:line-through}

    /* línea divisoria sutil entre meses en desktop */
    @media (min-width:640px){
      .fp-shell .flatpickr-days .dayContainer:nth-child(1){border-right:1px solid #e5e7eb}
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">
<header class="sticky top-0 bg-white shadow-sm z-50">
  <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
    <div class="text-2xl font-bold text-blue-600 flex items-center">
      <i class="fas fa-home mr-2"></i>
      <span>Sin beca<span class="text-gray-700"> no hay renta </span></span>
    </div>

    <div class="flex items-center gap-4">
      <button id="search-toggle" class="block md:hidden text-gray-700 text-xl"><i class="fas fa-search"></i></button>
      <button id="menu-toggle" class="md:hidden text-gray-700 text-2xl"><i class="fas fa-bars"></i></button>
    </div>

    <nav id="main-nav"
         class="hidden md:flex flex-col md:flex-row fixed md:static top-0 right-0 h-full md:h-auto w-3/4 md:w-auto bg-white md:bg-transparent shadow-lg md:shadow-none p-6 md:p-0 space-y-4 md:space-y-0 md:space-x-6 text-gray-700 font-medium">
      <button id="close-menu" class="md:hidden text-gray-500 text-2xl self-end mb-4"><i class="fas fa-times"></i></button>

      <a href="{{ route('visits.my') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Mi agenda</a>
      @auth
        <a href="{{ route('favorites.index') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Favoritos</a>
      @endauth
      <a href="{{ route('properties.map') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Mapa</a>

      @auth
        @if(auth()->user()->role === 'agent')
          <a href="{{ route('agent.home') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Panel de Agente</a>
        @else
          <a href="{{ route('agent.view') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Modo vendedor</a>
        @endif
        <a href="{{ url('/dashboard') }}" class="block bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 text-center">Perfil</a>
      @else
        <a href="{{ route('agent.view') }}" class="block px-4 py-2 rounded-full hover:bg-blue-50 hover:text-blue-600">Modo vendedor</a>
        <button type="button" onclick="openLoginModal()" class="block bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 text-center">Iniciar sesión</button>
      @endauth
    </nav>
  </div>
</header>

<main class="max-w-4xl mx-auto mt-10 px-6">
  <div class="mb-4">
    <h1 class="text-3xl font-bold">{{ $property->title }}</h1>
    <p class="text-md text-gray-600 mt-1">{{ $property->location }}</p>
  </div>

  <div class="mb-6">
    @if ($property->images->count() > 0)
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2 rounded-lg overflow-hidden" style="max-height:500px;">
        <a href="{{ asset('storage/' . $property->images->first()->image_path) }}" data-lightbox="property-gallery" data-title="{{ $property->title }}" class="col-span-2 row-span-2 relative group">
          <img src="{{ asset('storage/' . $property->images->first()->image_path) }}" alt="Imagen principal de {{ $property->title }}" class="w-full h-full object-cover hover:opacity-90 transition">
          <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
        </a>

        @foreach ($property->images->slice(1)->take(3) as $image)
          <a href="{{ asset('storage/' . $image->image_path) }}" data-lightbox="property-gallery" data-title="{{ $property->title }}" class="relative group">
            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Imagen de {{ $property->title }}" class="w-full h-full object-cover hover:opacity-90 transition">
            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
          </a>
        @endforeach

        @if ($property->images->count() >= 5)
          @php $fifthImage = $property->images->slice(4)->first(); @endphp
          <div class="relative group cursor-pointer" onclick="document.querySelector('[data-lightbox=\'property-gallery\']').click();">
            <img src="{{ asset('storage/' . $fifthImage->image_path) }}" alt="Ver más imágenes de {{ $property->title }}" class="w-full h-full object-cover filter grayscale hover:filter-none transition-all duration-300">
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-lg font-semibold">+ {{ $property->images->count() - 4 }} fotos</div>
          </div>
        @endif

        @if ($property->images->count() > 5)
          @foreach ($property->images->slice(5) as $image)
            <a href="{{ asset('storage/' . $image->image_path) }}" data-lightbox="property-gallery" data-title="{{ $property->title }}" class="hidden"></a>
          @endforeach
        @endif
      </div>
    @else
      <div class="col-span-full bg-gray-200 h-64 flex items-center justify-center rounded-lg">
        <p class="text-gray-500">No hay imágenes disponibles.</p>
      </div>
    @endif
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2">
      <div class="flex space-x-4 text-gray-700 border-t border-b py-3 mb-4">
        @if($property->bedrooms) <span>&#128719;️ {{ $property->bedrooms }} Habitaciones</span> @endif
        @if($property->bathrooms) <span>&#128705; {{ $property->bathrooms }} Baños</span> @endif
      </div>

      <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Descripción</h2>
      <p class="text-gray-700 leading-relaxed">{{ $property->description ?? 'No hay descripción disponible.' }}</p>

      <h2 class="text-2xl font-semibold border-b pb-2 mt-8 mb-4">Lo que ofrece este lugar</h2>
      @php $groupedAmenities = $property->amenities->groupBy('category.name'); @endphp
      @forelse ($groupedAmenities as $categoryName => $amenities)
        <div class="mt-4">
          <h4 class="font-semibold text-lg mb-2">{{ $categoryName }}</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
            @foreach ($amenities as $amenity)
              <div class="text-gray-700">{{ $amenity->name }}</div>
            @endforeach
          </div>
        </div>
      @empty
        <p class="text-gray-500">No se especificaron amenidades.</p>
      @endforelse>

      {{-- Reseñas --}}
      @if($property->listing_type === 'rent')
        <h2 class="text-2xl font-semibold border-b pb-2 mt-8 mb-4">Reseñas</h2>

        @if(session('success'))
          <div class="mb-3 p-3 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="mb-3 p-3 rounded bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-3 p-3 rounded bg-yellow-50 text-yellow-800 border border-yellow-200">
            <ul class="list-disc ml-5">
              @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
          </div>
        @endif

        @php
          $count = $property->reviews->count();
          $avg   = $count ? round($property->reviews->avg('overall'), 2) : null;
        @endphp

        <div class="mb-4">
          @if($count)
            <div class="text-lg font-semibold">⭐ {{ $avg }} / 5 · {{ $count }} reseña{{ $count>1?'s':'' }}</div>
          @else
            <div class="text-gray-500">Aún no hay reseñas.</div>
          @endif
        </div>

        <div class="space-y-4">
          @foreach($property->reviews->take(5) as $rev)
            <div class="p-4 bg-white rounded-lg shadow border">
              <div class="flex items-center justify-between">
                <div class="font-semibold">{{ $rev->author->name ?? 'Usuario' }}</div>
                <div>⭐ {{ number_format($rev->overall,1) }}</div>
              </div>
              @if($rev->comment)<p class="text-gray-700 mt-2">{{ $rev->comment }}</p>@endif
              <div class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($rev->published_at ?? $rev->created_at)->diffForHumans() }}</div>
            </div>
          @endforeach
        </div>

        @auth
          @php
            $eligibleReservation = \App\Models\PropertyReservation::where('property_id',$property->id)
              ->where('user_id',auth()->id())
              ->where('end_date','<',now())
              ->whereNotIn('id',\App\Models\Review::select('reservation_id'))
              ->latest()->first();
          @endphp

          @if($eligibleReservation)
            <h3 class="text-xl font-semibold mt-6 mb-2">Escribe tu reseña</h3>
            <form method="POST" action="{{ route('reviews.store',$property) }}" class="space-y-3">
              @csrf
              <input type="hidden" name="reservation_id" value="{{ $eligibleReservation->id }}">
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach (['cleanliness'=>'Limpieza','accuracy'=>'Precisión','communication'=>'Comunicación','location'=>'Ubicación','value'=>'Valor','checkin'=>'Check-in'] as $key=>$label)
                  <label class="block">
                    <span class="text-gray-700">{{ $label }}</span>
                    <select name="{{ $key }}" class="mt-1 block w-full border rounded p-2" required>
                      @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
                    </select>
                  </label>
                @endforeach
              </div>
              <label class="block">
                <span class="text-gray-700">Comentario (opcional)</span>
                <textarea name="comment" class="mt-1 block w-full border rounded p-2" rows="3"></textarea>
              </label>
              <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enviar reseña</button>
            </form>
          @endif
        @endauth
      @endif
    </div>

    <div class="md:col-span-1">
      <div class="bg-white p-6 rounded-lg shadow-md border sticky top-28">
        <p class="text-2xl font-bold">${{ number_format($property->price, 2) }}</p>
        @if($property->listing_type == 'rent')
          <p class="text-gray-500">Precio de renta</p>
        @elseif($property->listing_type == 'sale')
          <p class="text-gray-500">Precio de venta</p>
        @endif

        @if($property->listing_type == 'sale')
          <button type="button" class="w-full border border-blue-500 text-blue-600 py-3 rounded-lg mt-3 hover:bg-blue-50 font-semibold cursor-not-allowed" title="La mensajería se habilitará próximamente">
            Contactar con el agente
          </button>
        @elseif($property->listing_type == 'rent')
          <button id="reserveButton" class="w-full bg-blue-500 text-white py-3 rounded-lg mt-4 hover:bg-blue-600 font-semibold">
            Reservar
          </button>
        @endif

        @auth
          @php $isFav = auth()->user()->favoriteProperties()->where('properties.id',$property->id)->exists(); @endphp
          <div class="mt-4">
            <form method="POST" action="{{ $isFav ? route('favorites.destroy',$property) : route('favorites.store',$property) }}" id="fav-fallback-form" class="hidden">
              @csrf @if($isFav) @method('DELETE') @endif
            </form>
            <button id="fav-btn"
                    class="w-full border border-blue-500 text-blue-600 py-3 rounded-lg hover:bg-blue-50 font-semibold"
                    data-toggle-url="{{ route('favorites.toggle',$property) }}"
                    data-state="{{ $isFav ? 'on' : 'off' }}"
                    onclick="if(!window.toggleFavorite){ document.getElementById('fav-fallback-form').submit(); }">
              <span id="fav-icon">{{ $isFav ? '★' : '☆' }}</span>
              <span id="fav-text">{{ $isFav ? 'Quitar de favoritos' : 'Agregar a favoritos' }}</span>
            </button>
          </div>
        @endauth
      </div>
    </div>
  </div>

  <div class="mt-8">
    <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Ubicación</h2>
    <div id="map"></div>
  </div>
</main>

<!-- Modal visita -->
<div id="visitModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1000;">
  <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border-radius:8px;min-width:300px;">
    <h3 class="text-lg font-semibold mb-4">Selecciona fecha y hora para tu visita</h3>
    <input type="datetime-local" id="visitDateTime" class="w-full p-2 border rounded mb-4">
    <div class="flex gap-2">
      <button onclick="scheduleVisit()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Agendar Visita</button>
      <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</button>
    </div>
  </div>
</div>

<!-- Modal reserva -->
<div id="reservationModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1000;overflow-y:auto;">
  <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4">
      <div class="mb-4 sm:mb-0">
        <h2 id="modalNightCount" class="text-2xl font-bold">Selecciona fechas</h2>
        <p id="modalDateHint" class="text-gray-500 text-sm">Agrega tus fechas de viaje</p>
      </div>
      <div class="flex space-x-2 flex-shrink-0">
        <div class="border rounded-lg p-2 w-1/2 sm:w-36">
          <label class="text-xs font-bold text-gray-600 block">LLEGADA</label>
          <input id="modalStartDate" class="text-sm w-full outline-none" placeholder="dd/mm/aaaa" readonly>
        </div>
        <div class="border rounded-lg p-2 w-1/2 sm:w-36">
          <label class="text-xs font-bold text-gray-600 block">SALIDA</label>
          <input id="modalEndDate" class="text-sm w-full outline-none" placeholder="dd/mm/aaaa" readonly>
        </div>
      </div>
    </div>

    <div class="fp-shell">
      <input id="dateRange" class="fp-hidden-input" aria-hidden="true" tabindex="-1"/>
    </div>

    <div id="priceSummary" class="mt-4 p-3 bg-gray-50 rounded border hidden">
      <p class="font-semibold">Resumen de reserva:</p>
      <p id="nightsCount">Noches: 0</p>
      <p id="totalPrice">Total: $0.00 MXN</p>
    </div>

    <div class="text-xs text-gray-500 mt-2">*Los días no disponibles aparecen deshabilitados.</div>

    <div class="flex justify-between items-center mt-6 pt-4 border-t">
      <button id="clearDatesBtn" type="button" class="font-semibold underline text-sm hover:bg-gray-100 p-2 rounded">
        Borrar fechas
      </button>
      <div class="flex gap-2">
        <button type="button" onclick="closeReservationModal()" class="font-semibold px-5 py-2 rounded-lg hover:bg-gray-100 text-sm">
          Cancelar
        </button>
        <button type="button" id="confirmReservationBtn" class="bg-green-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-green-700 text-sm disabled:opacity-50">
          Confirmar Reserva
        </button>
      </div>
    </div>
  </div>
</div>

<form id="reservationPreviewForm" action="{{ route('reservations.preview') }}" method="POST" class="hidden">
  @csrf
  <input type="hidden" name="property_id" value="{{ $property->id }}">
  <input type="hidden" name="start_date" id="checkout_start_date">
  <input type="hidden" name="end_date" id="checkout_end_date">
</form>

<footer class="bg-gray-800 text-white py-8 mt-auto">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div class="md:col-span-2">
        <div class="flex items-center text-white font-bold text-xl mb-4">
          <i class="fas fa-home mr-2"></i><span>SIN BECA NO HAY RENTA</span>
        </div>
        <p class="text-gray-300 mb-4">Tu plataforma confiable para la gestión inmobiliaria…</p>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div>
        <h3 class="font-semibold text-lg mb-4">Navegación</h3>
        <ul class="space-y-2">
          <li><a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white">Mis Visitas</a></li>
          <li><a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white">Mapa</a></li>
          <li><a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white">Panel de Agente</a></li>
          <li><a href="{{ route('agent.view') }}" class="text-gray-300 hover:text-white">Modo Vendedor</a></li>
        </ul>
      </div>
      <div>
        <h3 class="font-semibold text-lg mb-4">Contacto</h3>
        <ul class="space-y-2 text-gray-300">
          <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> soporte@sinbeca.com</li>
          <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 (555) 123-4567</li>
          <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Ciudad, País</li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-700 mt-8 pt-6">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-300 text-sm">&copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <a href="#" class="text-gray-300 hover:text-white text-sm">Privacidad</a>
          <a href="#" class="text-gray-300 hover:text-white text-sm">Términos</a>
          <a href="#" class="text-gray-300 hover:text-white text-sm">Cookies</a>
        </div>
      </div>
    </div>
  </div>
</footer>

@php
  /* ===== Rangos NO disponibles (checkout libre) ===== */
  $blocked = [];

  if ($property->status === 'rented' && $property->rented_until) {
      $blocked[] = [
          'from' => now()->format('Y-m-d'),
          'to'   => $property->rented_until->copy()->subDay()->format('Y-m-d'),  // <-- FIX: Y-m-d
      ];
  }

  $reservas = $property->reservations()
      ->whereIn('status', ['paid','confirmed','completed'])
      ->orderBy('start_date')
      ->get();

  foreach ($reservas as $r) {
      $from = \Illuminate\Support\Carbon::parse($r->start_date)->format('Y-m-d');
      $to   = \Illuminate\Support\Carbon::parse($r->end_date)->subDay()->format('Y-m-d');
      $blocked[] = ['from'=>$from,'to'=>$to];
  }
@endphp

<script>
  // ================== Variables base ==================
  const propertyLocation = { lat: {{ $property->latitude }}, lng: {{ $property->longitude }} };
  const propertyId    = {{ $property->id }};
  const propertyPrice = {{ $property->price }};

  const blockedRanges = @json($blocked);

  // ================== Google Maps ==================
  fetch('/maps-key').then(res=>res.json()).then(data=>{
    const script=document.createElement('script');
    script.src=`https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
    script.async=true; document.head.appendChild(script);
  });
  function initMap(){
    const map=new google.maps.Map(document.getElementById("map"),{center:propertyLocation,zoom:16});
    new google.maps.Marker({map,position:propertyLocation,title:"{{ $property->title }}"});
  }

  // ================== VISITAS (oculto en UI) ==================
  function openVisitCalendar(){
    const now=new Date();
    const defaultDate=new Date(); defaultDate.setDate(now.getDate()+2); defaultDate.setHours(10,0,0,0);
    const input=document.getElementById('visitDateTime');
    input.min=now.toISOString().slice(0,16);
    input.value=defaultDate.toISOString().slice(0,16);
    document.getElementById('visitModal').style.display='block';
  }
  function closeModal(){ document.getElementById('visitModal').style.display='none'; }
  function scheduleVisit(){
    const visitDate=document.getElementById('visitDateTime').value;
    if(!visitDate){ alert('Por favor selecciona una fecha y hora'); return; }
    fetch('/visits',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body:JSON.stringify({ property_id:propertyId, agent_id:{{ $property->user_id }}, visit_date:visitDate })
    }).then(r=>r.json()).then(data=>{
      if(data.success){ alert('Visita agendada'); closeModal(); }
      else{ alert('Error: '+data.message); }
    }).catch(()=>alert('Error al agendar la visita'));
  }

  // ================== RESERVA (Flatpickr) ==================
  let selectedRange={start:null,end:null}, fpInstance=null;

  function openReservation(){
    const modal=document.getElementById('reservationModal');
    modal.style.display='block';
    if(!fpInstance) initCalendar();
    document.getElementById('priceSummary').classList.add('hidden');
  }
  function closeReservationModal(){
    document.getElementById('reservationModal').style.display='none';
  }

  function initCalendar(){
    const shell=document.querySelector('.fp-shell');
    const disable = blockedRanges.map(r=>({from:r.from,to:r.to}));
    const confirmBtn = document.getElementById('confirmReservationBtn');

    const esLocale={
      weekdays:{ shorthand:['D','L','M','M','J','V','S'], longhand:['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] },
      months:{ shorthand:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'], longhand:['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] },
      firstDayOfWeek:1, rangeSeparator:' a '
    };

    const formatDate = (date) => {
      if (!date) return '';
      const d=new Date(date);
      const dd=String(d.getDate()).padStart(2,'0');
      const mm=String(d.getMonth()+1).padStart(2,'0');
      const yy=d.getFullYear();
      return `${dd}/${mm}/${yy}`;
    };

    fpInstance = flatpickr('#dateRange',{
      mode:'range',
      inline:true,
      static:true,
      appendTo:shell,
      minDate:'today',
      dateFormat:'Y-m-d',
      altInput:false,
      showMonths: window.innerWidth>=640 ? 2 : 1,
      disableMobile:true,
      disable,
      locale:esLocale,
      nextArrow:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:1.25rem;height:1.25rem;"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>',
      prevArrow:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:1.25rem;height:1.25rem;"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L8.842 10l3.948 3.71a.75.75 0 11-1.06 1.06l-4.5-4.25a.75.75 0 010-1.06l4.5-4.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>',
      onChange:(dates)=>{
        if(dates.length===1){
          selectedRange={start:dates[0],end:null};
          document.getElementById('modalStartDate').value = formatDate(dates[0]);
          document.getElementById('modalEndDate').value = '';
          document.getElementById('modalNightCount').textContent = 'Selecciona fecha de salida';
          document.getElementById('modalDateHint').textContent = 'Agrega tu fecha de salida';
          document.getElementById('priceSummary').classList.add('hidden');
          confirmBtn.disabled = true;
        } else if(dates.length===2){
          const [start,end]=dates.sort((a,b)=>a-b);
          selectedRange={start,end};
          const ms=1000*60*60*24;
          const nights=Math.round((end-start)/ms);
          const total=(nights*propertyPrice).toFixed(2);

          document.getElementById('modalStartDate').value = formatDate(start);
          document.getElementById('modalEndDate').value = formatDate(end);
          document.getElementById('modalNightCount').textContent = `${nights} noche${nights>1?'s':''}`;
          document.getElementById('modalDateHint').textContent = `${formatDate(start)} - ${formatDate(end)}`;

          document.getElementById('nightsCount').textContent='Noches: '+nights;
          document.getElementById('totalPrice').textContent='Total: $'+total+' MXN';
          document.getElementById('priceSummary').classList.remove('hidden');
          confirmBtn.disabled = false;
        } else {
          selectedRange={start:null,end:null};
          document.getElementById('modalStartDate').value = '';
          document.getElementById('modalEndDate').value = '';
          document.getElementById('modalNightCount').textContent = 'Selecciona fechas';
          document.getElementById('modalDateHint').textContent = 'Agrega tus fechas de viaje';
          document.getElementById('priceSummary').classList.add('hidden');
          confirmBtn.disabled = true;
        }
      }
    });

    confirmBtn.disabled = true;

    window.addEventListener('resize',()=>{
      if(!fpInstance) return;
      const months = window.innerWidth>=640 ? 2 : 1;
      fpInstance.set('showMonths', months);
    });
  }

  // Checkout al preview
  window.goToCheckout=function(){
    if(!selectedRange.start || !selectedRange.end){
      alert('Selecciona fechas de entrada y salida'); return;
    }
    const fmt=d=>d.toISOString().split('T')[0];
    document.getElementById('checkout_start_date').value=fmt(selectedRange.start);
    document.getElementById('checkout_end_date').value=fmt(selectedRange.end); // <-- FIX
    document.getElementById('reservationPreviewForm').submit();
  };

  // Listeners
  document.getElementById('reserveButton')?.addEventListener('click', openReservation);
  document.getElementById('confirmReservationBtn')?.addEventListener('click', ()=>window.goToCheckout());
  document.getElementById('clearDatesBtn')?.addEventListener('click', () => {
    if (fpInstance) {
      fpInstance.clear();
      document.getElementById('modalStartDate').value='';
      document.getElementById('modalEndDate').value='';
      document.getElementById('modalNightCount').textContent='Selecciona fechas';
      document.getElementById('modalDateHint').textContent='Agrega tus fechas de viaje';
      document.getElementById('priceSummary').classList.add('hidden');
      document.getElementById('confirmReservationBtn').disabled = true;
    }
  });

  // Cerrar modales al clicar fuera o con Escape
  window.addEventListener('click',(e)=>{
    if(e.target===document.getElementById('visitModal')) closeModal();
    if(e.target===document.getElementById('reservationModal')) closeReservationModal();
  });
  window.addEventListener('keydown',(e)=>{
    if(e.key==='Escape'){ closeModal(); closeReservationModal(); }
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<script>
  window.toggleFavorite = async function(){
    const btn=document.getElementById('fav-btn'); if(!btn) return;
    const url=btn.dataset.toggleUrl, icon=document.getElementById('fav-icon'), text=document.getElementById('fav-text');
    try{
      const res=await fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}});
      const data=await res.json();
      if(data.ok){
        const on=data.favorited===true; btn.dataset.state=on?'on':'off';
        icon.textContent=on?'★':'☆'; text.textContent=on?'Quitar de favoritos':'Agregar a favoritos';
      }
    }catch(e){}
  };
  document.getElementById('fav-btn')?.addEventListener('click',function(ev){
    ev.preventDefault(); if(window.fetch) toggleFavorite(); else document.getElementById('fav-fallback-form').submit();
  });
</script>
</body>
</html>
