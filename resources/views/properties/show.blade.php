<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $property->title }}</title>

  {{-- Estilos externos --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* Lightbox: flechas siempre visibles y bien posicionadas */
    .lb-nav a.lb-prev,
    .lb-nav a.lb-next {
      opacity: 1 !important;
    }

    .lb-nav a.lb-prev { left: 15px !important; }
    .lb-nav a.lb-next { right: 15px !important; }

    /* ===== Modal y mapa ===== */
    #reservationModal > div{
      max-width: 720px;
      width:calc(100% - 32px);
      max-height:calc(100vh - 80px);
      overflow:auto;
      border-radius:16px;
      padding:24px;
    }
    #map{ height:400px; width:100%; border-radius:12px; }

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

    /* ==== Fechas no disponibles (forzado, siempre gris) ==== */
    .fp-shell .flatpickr-day.flatpickr-disabled,
    .fp-shell .flatpickr-day.flatpickr-disabled:hover,
    .fp-shell .flatpickr-day.disabled,
    .fp-shell .flatpickr-day.disabled:hover {
      background-color: #f3f4f6 !important;
      color: #9ca3af !important;
      border-color: transparent !important;
      cursor: not-allowed !important;
      opacity: 1 !important;
      text-decoration: none !important;
    }

    .fp-shell .flatpickr-day.flatpickr-disabled.inRange,
    .fp-shell .flatpickr-day.disabled.inRange {
      background-color: #e5e7eb !important;
      color: #9ca3af !important;
    }

    @media (min-width:640px){
      .fp-shell .flatpickr-days .dayContainer:nth-child(1){border-right:1px solid #e5e7eb}
    }

    /* Quitar animaciones de aparición del lightbox */
    #lightbox,
    #lightbox .lb-outerContainer,
    #lightbox .lb-container {
      -webkit-transition: none !important;
      transition: none !important;
    }

    /* Botones de anterior/siguiente fijos a los lados (no en la imagen) */
    #lightbox .lb-nav a.lb-prev,
    #lightbox .lb-nav a.lb-next {
      position: fixed;
      top: 50%;
      transform: translateY(-50%);
      width: 3rem;
      height: 3rem;
      margin: 0;
      padding: 0;
      opacity: 1 !important;
      background: rgba(15, 23, 42, 0.9);
      border-radius: 9999px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background-image: none !important;
      z-index: 10002;
    }

    #lightbox .lb-nav a.lb-prev { left: 1.5rem; }
    #lightbox .lb-nav a.lb-next { right: 1.5rem; }

    #lightbox .lb-nav a.lb-prev::before,
    #lightbox .lb-nav a.lb-next::before {
      content: '';
      display: block;
      width: 0.75rem;
      height: 0.75rem;
      border-top: 2px solid #fff;
      border-right: 2px solid #fff;
    }

    #lightbox .lb-nav a.lb-prev::before {
      transform: rotate(-135deg);
      margin-left: 0.1rem;
    }

    #lightbox .lb-nav a.lb-next::before {
      transform: rotate(45deg);
      margin-right: 0.1rem;
    }

    @media (max-width: 640px) {
      #lightbox .lb-nav a.lb-prev,
      #lightbox .lb-nav a.lb-next {
        width: 2.5rem;
        height: 2.5rem;
      }
      #lightbox .lb-nav a.lb-prev { left: 0.75rem; }
      #lightbox .lb-nav a.lb-next { right: 0.75rem; }
    }

    /* Modal ligero para mensajes (éxito / error) */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      padding: 16px;
    }

    .modal-card {
      max-width: 480px;
      width: 100%;
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25);
    }
  </style>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-gray-800 dark:text-gray-100">

<x-main-header />

@php
  $isRent = $property->listing_type === 'rent';
  $isOwner = auth()->check() && auth()->id() === $property->user_id;
@endphp

<main class="max-w-6xl mx-auto mt-8 md:mt-10 px-4 lg:px-0 space-y-8 md:space-y-10 mb-10">

  {{-- Bloque superior: título + ubicación + galería --}}
  <section class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4 md:p-6">
    {{-- HEADER: título + rating + favorito --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <div>
        <div class="flex items-center gap-2 mb-2">
          @if($isRent)
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
              <i class="fa-solid fa-key text-[11px]"></i>
              En renta
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
              <i class="fa-solid fa-tags text-[11px]"></i>
              En venta
            </span>
          @endif
        </div>
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-50">
          {{ $property->title }}
        </h1>
        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
          <i class="fa-solid fa-location-dot text-xs text-blue-500"></i>
          <span>{{ $property->location }}</span>
        </p>
      </div>

      <div class="flex items-center gap-3 mt-1 md:mt-0">
        {{-- Rating si es renta --}}
        @if($isRent && $property->reviews->count())
          @php
            $count = $property->reviews->count();
            $avg   = round($property->reviews->avg('overall'), 2);
          @endphp
          <div class="flex items-center gap-1 text-xs md:text-sm">
            <span class="text-yellow-500"><i class="fa-solid fa-star"></i></span>
            <span class="font-semibold">{{ $avg }}</span>
            <span class="text-gray-500 dark:text-gray-400">· {{ $count }} reseña{{ $count>1?'s':'' }}</span>
          </div>
        @endif

        {{-- Botón de favoritos al nivel del título --}}
        @auth
          @if(!$isOwner)
            @php
              $isFav = auth()->user()->favoriteProperties()->where('properties.id',$property->id)->exists();
            @endphp

            <form method="POST"
                  action="{{ $isFav ? route('favorites.destroy',$property) : route('favorites.store',$property) }}"
                  id="fav-fallback-form"
                  class="hidden">
              @csrf
              @if($isFav) @method('DELETE') @endif
            </form>

            <button id="fav-btn"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full
                           bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700
                           shadow-sm hover:bg-pink-50 dark:hover:bg-pink-900/30 transition
                           text-gray-400"
                    data-toggle-url="{{ route('favorites.toggle',$property) }}"
                    data-state="{{ $isFav ? 'on' : 'off' }}"
                    onclick="if(!window.toggleFavorite){ document.getElementById('fav-fallback-form').submit(); }">
              <i id="fav-icon"
                 class="fa-heart {{ $isFav ? 'fa-solid text-pink-500' : 'fa-regular' }}"></i>
              <span id="fav-text" class="sr-only">
                {{ $isFav ? 'Quitar de favoritos' : 'Agregar a favoritos' }}
              </span>
            </button>
          @endif
        @endauth
      </div>
    </div>

    {{-- Galería --}}
    <div class="mt-3 md:mt-4">
      @php
        $imgs    = $property->images;
        $total   = $imgs->count();
        $hero    = $imgs->first();
        $tiles4  = $imgs->slice(1)->take(4)->values();
        while ($tiles4->count() < 4) { $tiles4->push(null); }
        $visible = 1 + $imgs->slice(1)->take(4)->count();
        $rest    = $imgs->slice($visible);
      @endphp

      @if ($total > 0)
        <div class="relative">
          <div class="grid grid-cols-4 gap-2 md:gap-3 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800">
            {{-- HERO 2x2 --}}
            <a id="open-gallery-anchor"
               href="{{ asset('storage/'.$hero->image_path) }}"
               data-lightbox="property-gallery"
               data-title="{{ $property->title }}"
               class="col-span-4 md:col-span-2 md:row-span-2 relative group">
              <div class="w-full h-full aspect-[4/3]">
                <img src="{{ asset('storage/'.$hero->image_path) }}"
                     alt="Imagen principal de {{ $property->title }}"
                     class="w-full h-full object-cover block">
              </div>
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </a>

            {{-- 4 tiles derecha --}}
            @foreach ($tiles4 as $img)
              @if ($img)
                <a href="{{ asset('storage/'.$img->image_path) }}"
                   data-lightbox="property-gallery"
                   data-title="{{ $property->title }}"
                   class="relative hidden md:block">
                  <div class="w-full h-full aspect-[4/3]">
                    <img src="{{ asset('storage/'.$img->image_path) }}"
                         alt="Imagen de {{ $property->title }}"
                         class="w-full h-full object-cover block">
                  </div>
                  <div class="absolute inset-0 bg-black/0 hover:bg-black/15 transition-colors"></div>
                </a>
              @else
                <div class="relative hidden md:block">
                  <div class="w-full h-full aspect-[4/3] bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <i class="fa-regular fa-image text-2xl text-gray-400 dark:text-gray-500"></i>
                  </div>
                </div>
              @endif
            @endforeach
          </div>

          {{-- CTA escritorio --}}
          <button type="button"
                  onclick="document.getElementById('open-gallery-anchor')?.click()"
                  class="hidden md:inline-flex items-center gap-2 text-xs md:text-sm font-semibold bg-white/95 dark:bg-gray-900/95 backdrop-blur px-3 py-2 rounded-full shadow-md border border-gray-200 dark:border-gray-700 absolute bottom-3 right-3">
            <i class="fa-solid fa-grip text-xs"></i>
            @if ($total > 5)
              Mostrar todas las fotos (+{{ $total - 5 }})
            @else
              Ver galería
            @endif
          </button>
        </div>

        {{-- CTA móvil --}}
        <button type="button"
                onclick="document.getElementById('open-gallery-anchor')?.click()"
                class="mt-3 w-full md:hidden inline-flex items-center justify-center gap-2 text-sm font-semibold bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 px-4 py-2 rounded-xl">
          <i class="fa-solid fa-grip"></i>
          @if ($total > 5)
            Mostrar todas las fotos (+{{ $total - 5 }})
          @else
            Ver galería
          @endif
        </button>

        {{-- Anclas ocultas restantes --}}
        @foreach ($rest as $img)
          <a href="{{ asset('storage/'.$img->image_path) }}"
             data-lightbox="property-gallery"
             data-title="{{ $property->title }}"
             class="hidden"></a>
        @endforeach
      @else
        <div class="bg-gray-100 dark:bg-gray-800 h-64 flex items-center justify-center rounded-2xl">
          <p class="text-gray-500 dark:text-gray-400">No hay imágenes disponibles.</p>
        </div>
      @endif
    </div>
  </section>

  {{-- Contenido principal: info + card lateral --}}
  <section class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
    {{-- Columna izquierda --}}
    <div class="md:col-span-2 space-y-6">
      {{-- Card de features --}}
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 px-5 py-4 flex flex-wrap gap-4 text-sm text-gray-700 dark:text-gray-200">
        @if($property->bedrooms)
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800">
            <i class="fa-solid fa-bed text-gray-500 dark:text-gray-400"></i>
            <span class="font-medium">{{ $property->bedrooms }} hab.</span>
          </span>
        @endif

        @if($property->bathrooms)
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-50 dark:bg-gray-800">
            <i class="fa-solid fa-bath text-gray-500 dark:text-gray-400"></i>
            <span class="font-medium">{{ $property->bathrooms }} baños</span>
          </span>
        @endif
      </div>

      {{-- Descripción + amenidades + reseñas --}}
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 md:p-6 space-y-6">
        {{-- Descripción --}}
        <section>
          <h2 class="text-xl md:text-2xl font-semibold mb-2 text-gray-900 dark:text-gray-50">Descripción</h2>
          <p class="text-gray-700 dark:text-gray-200 leading-relaxed">
            {{ $property->description ?? 'No hay descripción disponible.' }}
          </p>
        </section>

        {{-- Amenidades --}}
        <section>
          <h2 class="text-xl md:text-2xl font-semibold border-b border-gray-100 dark:border-gray-800 pb-2 mb-3 text-gray-900 dark:text-gray-50">
            Lo que ofrece este lugar
          </h2>
          @php $groupedAmenities = $property->amenities->groupBy('category.name'); @endphp
          @forelse ($groupedAmenities as $categoryName => $amenities)
            <div class="mt-3">
              <h4 class="font-semibold text-base mb-1.5 text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <span class="w-1 h-4 rounded-full bg-blue-500"></span>
                {{ $categoryName }}
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1.5 text-sm">
                @foreach ($amenities as $amenity)
                  <div class="text-gray-700 dark:text-gray-200 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    {{ $amenity->name }}
                  </div>
                @endforeach
              </div>
            </div>
          @empty
            <p class="text-gray-500 dark:text-gray-400">No se especificaron amenidades.</p>
          @endforelse
        </section>

        {{-- Reseñas (solo renta) --}}
        @if($isRent)
          <section>
            <h2 class="text-xl md:text-2xl font-semibold border-b border-gray-100 dark:border-gray-800 pb-2 mb-3 text-gray-900 dark:text-gray-50">
              Reseñas
            </h2>

            @if(session('success'))
              <div class="mb-3 p-3 rounded-xl bg-green-50 text-green-700 border border-green-200 text-sm">
                {{ session('success') }}
              </div>
            @endif
            @if(session('error'))
              <div class="mb-3 p-3 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm">
                {{ session('error') }}
              </div>
            @endif
            @if ($errors->any())
              <div class="mb-3 p-3 rounded-xl bg-yellow-50 text-yellow-800 border border-yellow-200 text-sm">
                <ul class="list-disc ml-5 space-y-0.5">
                  @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
              </div>
            @endif

            @php
              $count = $property->reviews->count();
              $avg   = $count ? round($property->reviews->avg('overall'), 2) : null;
            @endphp

            <div class="mb-3">
              @if($count)
                <div class="inline-flex items-center gap-2 text-sm font-semibold px-3 py-1.5 rounded-full bg-gray-50 dark:bg-gray-800">
                  <span class="text-yellow-500"><i class="fa-solid fa-star"></i></span>
                  <span>{{ $avg }} / 5</span>
                  <span class="text-gray-500 dark:text-gray-400">· {{ $count }} reseña{{ $count>1?'s':'' }}</span>
                </div>
              @else
                <div class="text-gray-500 dark:text-gray-400 text-sm">Aún no hay reseñas.</div>
              @endif
            </div>

            <div class="space-y-3">
              @foreach($property->reviews->take(5) as $rev)
                <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700">
                  <div class="flex items-center justify-between gap-2">
                    <div class="font-semibold text-sm text-gray-900 dark:text-gray-50">
                      {{ $rev->author->name ?? 'Usuario' }}
                    </div>
                    <div class="text-sm text-yellow-500 font-semibold">
                      ⭐ {{ number_format($rev->overall,1) }}
                    </div>
                  </div>
                  @if($rev->comment)
                    <p class="text-sm text-gray-700 dark:text-gray-200 mt-1.5">{{ $rev->comment }}</p>
                  @endif
                  <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                    {{ \Carbon\Carbon::parse($rev->published_at ?? $rev->created_at)->diffForHumans() }}
                  </div>
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
                <h3 class="text-lg font-semibold mt-5 mb-2 text-gray-900 dark:text-gray-50">Escribe tu reseña</h3>
                <form method="POST" action="{{ route('reviews.store',$property) }}" class="space-y-3">
                  @csrf
                  <input type="hidden" name="reservation_id" value="{{ $eligibleReservation->id }}">
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    @foreach (['cleanliness'=>'Limpieza','accuracy'=>'Precisión','communication'=>'Comunicación','location'=>'Ubicación','value'=>'Valor','checkin'=>'Check-in'] as $key=>$label)
                      <label class="block">
                        <span class="text-gray-700 dark:text-gray-200">{{ $label }}</span>
                        <select name="{{ $key }}"
                                class="mt-1 block w-full border rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100"
                                required>
                          @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }}</option> @endfor
                        </select>
                      </label>
                    @endforeach
                  </div>
                  <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-200">Comentario (opcional)</span>
                    <textarea name="comment"
                              class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100"
                              rows="3"></textarea>
                  </label>
                  <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-semibold">
                    Enviar reseña
                  </button>
                </form>
              @endif
            @endauth
          </section>
        @endif
      </div>
    </div>

    {{-- Columna derecha: card de reserva/contacto --}}
    <div class="md:col-span-1">
      <div class="bg-white dark:bg-gray-900 p-5 md:p-6 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 sticky top-24 space-y-4">
        <div>
          <p class="text-2xl font-bold text-gray-900 dark:text-gray-50">
            ${{ number_format($property->price, 2) }}
          </p>
          @if($property->listing_type == 'rent')
            <p class="text-gray-500 dark:text-gray-400 text-sm">Precio de renta</p>
          @elseif($property->listing_type == 'sale')
            <p class="text-gray-500 dark:text-gray-400 text-sm">Precio de venta</p>
          @endif
        </div>

        @if($isOwner)
          <div class="p-3 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-info mt-0.5"></i>
            <p class="leading-snug">Eres el dueño de esta propiedad; no es posible reservarla, contactarte a ti mismo ni guardarla como favorita.</p>
          </div>
        @elseif($property->listing_type == 'sale')
          {{-- Venta: solo contacto/chat --}}
          @auth
            <a href="{{ route('chat.show', $property) }}"
               class="block w-full text-center border border-blue-500 text-blue-600 dark:text-blue-400 py-3 rounded-xl mt-1 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-semibold text-sm">
              Contactar con el agente
            </a>
          @else
            <button type="button" onclick="openLoginModal()"
                    class="w-full border border-blue-500 text-blue-600 dark:text-blue-400 py-3 rounded-xl mt-1 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-semibold text-sm">
              Inicia sesión para contactar
            </button>
          @endauth

        @elseif($property->listing_type == 'rent')
          {{-- Renta: reservar + chat --}}
          <button id="reserveButton"
                  class="w-full bg-blue-600 text-white py-3 rounded-xl mt-1 hover:bg-blue-700 font-semibold text-sm flex items-center justify-center gap-2">
            <i class="fa-solid fa-calendar-check text-sm"></i>
            <span>Reservar</span>
          </button>

          @auth
            <a href="{{ route('chat.show', $property) }}"
               class="block w-full text-center border border-blue-500 text-blue-600 dark:text-blue-400 py-3 rounded-xl mt-3 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-semibold text-sm">
              Contactar para reservar
            </a>
          @else
            <button type="button" onclick="openLoginModal()"
                    class="w-full border border-blue-500 text-blue-600 dark:text-blue-400 py-3 rounded-xl mt-3 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-semibold text-sm">
              Inicia sesión para contactar
            </button>
          @endauth
        @endif
      </div>
    </div>
  </section>

  {{-- Mapa / ubicación --}}
  <section class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 md:p-6">
    <h2 class="text-xl md:text-2xl font-semibold border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 text-gray-900 dark:text-gray-50 flex items-center gap-2">
      <i class="fa-solid fa-map-location-dot text-blue-500"></i>
      <span>Ubicación</span>
    </h2>
    <div id="map" class="overflow-hidden"></div>
  </section>
</main>

{{-- Modal visita --}}
<div id="visitModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,.65);z-index:1000;">
  <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;border-radius:16px;min-width:320px;max-width:420px;padding:20px;"
       class="shadow-xl">
    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-gray-900">
      <i class="fa-solid fa-calendar-day text-blue-500"></i>
      <span>Selecciona fecha y hora para tu visita</span>
    </h3>
    <input type="datetime-local" id="visitDateTime" class="w-full p-2 border rounded-lg mb-4 text-sm">
    <div class="flex gap-2 justify-end">
      <button onclick="closeModal()"
              class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm">
        Cancelar
      </button>
      <button onclick="scheduleVisit()"
              class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 text-sm">
        Agendar visita
      </button>
    </div>
  </div>
</div>

{{-- Modal reserva --}}
<div id="reservationModal"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,.65);z-index:1000;overflow-y:auto;">
  <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;"
       class="shadow-2xl">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 gap-3">
      <div class="mb-2 sm:mb-0">
        <h2 id="modalNightCount" class="text-2xl font-bold text-gray-900">Selecciona fechas</h2>
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

    <div id="priceSummary" class="mt-4 p-3 bg-gray-50 rounded-lg border text-sm hidden">
      <p class="font-semibold mb-1">Resumen de reserva:</p>
      <p id="nightsCount">Noches: 0</p>
      <p id="totalPrice">Total: $0.00 MXN</p>
    </div>

    <div class="text-xs text-gray-500 mt-2">*Los días no disponibles aparecen deshabilitados.</div>

    <div class="flex justify-between items-center mt-6 pt-4 border-t">
      <button id="clearDatesBtn" type="button"
              class="font-semibold underline text-sm hover:bg-gray-100 px-2 py-1 rounded">
        Borrar fechas
      </button>
      <div class="flex gap-2">
        <button type="button" onclick="closeReservationModal()"
                class="font-semibold px-4 py-2 rounded-lg hover:bg-gray-100 text-sm">
          Cancelar
        </button>
        <button type="button" id="confirmReservationBtn"
                class="bg-green-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-green-700 text-sm disabled:opacity-50">
          Confirmar reserva
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

{{-- FOOTER --}}
<x-main-footer />

{{-- Modal genérico para feedback --}}
<div id="feedbackModal" class="modal-overlay">
  <div class="modal-card">
    <div class="flex items-start gap-3">
      <div id="feedbackIconWrapper" class="mt-1">
        <span id="feedbackIcon" class="w-10 h-10 inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-600">
          <i class="fa-solid fa-circle-info"></i>
        </span>
      </div>
      <div class="flex-1">
        <h3 id="feedbackTitle" class="text-lg font-semibold text-gray-900">Aviso</h3>
        <p id="feedbackMessage" class="text-gray-700 mt-1 leading-relaxed">Mensaje.</p>
      </div>
    </div>
    <div class="flex justify-end mt-6">
      <button type="button"
              onclick="closeFeedbackModal()"
              class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700">
        Entendido
      </button>
    </div>
  </div>
</div>

@php
  /* ===== Rangos NO disponibles (checkout libre) ===== */
  $blocked = [];

  if ($property->status === 'rented' && $property->rented_until) {
      $blocked[] = [
          'from' => now()->format('Y-m-d'),
          'to'   => $property->rented_until->copy()->subDay()->format('Y-m-d'),
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
  // ===== Modal de feedback (sin alertas nativas) =====
  const feedbackModal = document.getElementById('feedbackModal');
  const feedbackTitle = document.getElementById('feedbackTitle');
  const feedbackMessage = document.getElementById('feedbackMessage');
  const feedbackIcon = document.getElementById('feedbackIcon');

  function showFeedbackModal(message, { title = 'Aviso', variant = 'info' } = {}) {
    const styles = {
      success: { bg: 'bg-emerald-100', text: 'text-emerald-700', icon: 'fa-circle-check' },
      error:   { bg: 'bg-red-100',     text: 'text-red-700',     icon: 'fa-circle-xmark' },
      info:    { bg: 'bg-blue-100',    text: 'text-blue-700',    icon: 'fa-circle-info' },
    };

    const current = styles[variant] || styles.info;
    feedbackTitle.textContent = title;
    feedbackMessage.textContent = message;

    feedbackIcon.className = `w-10 h-10 inline-flex items-center justify-center rounded-full ${current.bg} ${current.text}`;
    feedbackIcon.querySelector('i').className = `fa-solid ${current.icon}`;

    feedbackModal.style.display = 'flex';
  }

  function closeFeedbackModal() {
    feedbackModal.style.display = 'none';
  }

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

  // ================== VISITAS ==================
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
    if(!visitDate){
      showFeedbackModal('Por favor selecciona una fecha y hora para agendar tu visita.', { variant: 'info', title: 'Selecciona fecha' });
      return;
    }
    fetch('/visits',{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body:JSON.stringify({ property_id:propertyId, agent_id:{{ $property->user_id }}, visit_date:visitDate })
    }).then(r=>r.json()).then(data=>{
      if(data.success){
        showFeedbackModal('Tu visita ha sido agendada correctamente.', { title: 'Visita agendada', variant: 'success' });
        closeModal();
      }
      else{
        const msg = data.message || 'No pudimos agendar tu visita. Inténtalo de nuevo.';
        showFeedbackModal(msg, { title: 'No se pudo agendar', variant: 'error' });
      }
    }).catch(()=>showFeedbackModal('Error al agendar la visita. Intenta nuevamente en unos minutos.', { title: 'Error de red', variant: 'error' }));
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
      showFeedbackModal('Selecciona fechas de entrada y salida para continuar con tu reserva.', { variant: 'info', title: 'Faltan fechas' });
      return;
    }
    const fmt=d=>d.toISOString().split('T')[0];
    document.getElementById('checkout_start_date').value=fmt(selectedRange.start);
    document.getElementById('checkout_end_date').value=fmt(selectedRange.end);
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
    if(e.target===feedbackModal) closeFeedbackModal();
  });
  window.addEventListener('keydown',(e)=>{
    if(e.key==='Escape'){ closeModal(); closeReservationModal(); closeFeedbackModal(); }
  });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<script>
  // Config del lightbox: sin animaciones y con navegación cómoda
  lightbox.option({
    resizeDuration: 0,
    fadeDuration: 0,
    imageFadeDuration: 0,
    wrapAround: true,
    alwaysShowNavOnTouchDevices: true
  });
</script>

<script>
  window.toggleFavorite = async function(){
    const btn=document.getElementById('fav-btn'); if(!btn) return;
    const url=btn.dataset.toggleUrl, icon=document.getElementById('fav-icon'), text=document.getElementById('fav-text');
    try{
      const res=await fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}});
      const data=await res.json();
      if(data.ok){
        const on=data.favorited===true; btn.dataset.state=on?'on':'off';
        // Cambiamos estilo del ícono y texto accesible
        icon.classList.toggle('fa-solid', on);
        icon.classList.toggle('fa-regular', !on);
        icon.classList.toggle('text-pink-500', on);
        icon.classList.toggle('text-gray-400', !on);
        text.textContent = on ? 'Quitar de favoritos' : 'Agregar a favoritos';
      }
    }catch(e){}
  };
  document.getElementById('fav-btn')?.addEventListener('click',function(ev){
    ev.preventDefault(); if(window.fetch) toggleFavorite(); else document.getElementById('fav-fallback-form').submit();
  });
</script>
</body>
</html>
