<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Propiedades - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Necesario para AJAX si usas layout --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- Header --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
        {{-- ... (Contenido del header sin cambios) ... --}}
         <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-8">
                    <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                        <a href="{{ url('/') }}">
                        <i class="fas fa-home mr-2"></i>
                        Sin beca<span class="text-gray-700"> no hay renta </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 border-l border-gray-300 pl-8">Mis Propiedades</h1>
                </div>
                <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                    {{-- Ajusta estos enlaces según tus rutas --}}
                    <a href="{{ route('agent.home') ?? '#' }}" class="hover:text-blue-600 transition">Panel de Agente</a>
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Modo Visitante</a>
                    <a href="{{ url('/dashboard') }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
                </nav>
            </div>

        </div>
    </header>

    {{-- Contenido Principal --}}
    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        {{-- Barra de acciones (Filtros y Añadir) --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
           {{-- ... (Contenido de filtros y botón añadir sin cambios) ... --}}
           <div class="flex flex-wrap gap-3">
            <a href="{{ route('properties.my') }}"
            class="px-5 py-2 rounded-full border {{ !request('type') ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium text-sm sm:text-base">
                Todas
            </a>
            <a href="{{ route('properties.my', ['type' => 'rent']) }}"
            class="px-5 py-2 rounded-full border {{ request('type') == 'rent' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium text-sm sm:text-base">
                Solo Renta
            </a>
            <a href="{{ route('properties.my', ['type' => 'sale']) }}"
            class="px-5 py-2 rounded-full border {{ request('type') == 'sale' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }} transition-colors font-medium text-sm sm:text-base">
                Solo Venta
            </a>
        </div>

            <a href="{{ route('properties.create') }}"
               class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-5 py-2 transition-colors duration-200 w-full sm:w-auto justify-center">
                <i class="fas fa-plus"></i>
                <span>Añadir Nueva Propiedad</span>
            </a>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        {{-- Lista de Propiedades --}}
        <div class="space-y-5">
            @forelse ($properties as $property)
            @php
                // Mapas de estilos para el badge de estatus (Tailwind)
                $badgeMap = [
                    'available'   => 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200',
                    'unavailable' => 'bg-gray-100 text-gray-700 ring-1 ring-gray-300',
                    'rented'      => 'bg-cyan-100 text-cyan-800 ring-1 ring-cyan-200',
                    'sold'        => 'bg-slate-200 text-slate-800 ring-1 ring-slate-300',
                ];
                $dotMap = [
                    'available'   => 'bg-emerald-500',
                    'unavailable' => 'bg-gray-400',
                    'rented'      => 'bg-cyan-500',
                    'sold'        => 'bg-slate-500',
                ];
            @endphp

            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                <div class="p-6">
                    {{-- Header de la propiedad --}}
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                        {{-- Izquierda: Título y Tipo --}}
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                <a href="{{ route('properties.show', $property) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $property->title }}
                                </a>
                            </h3>
                            {{-- Badge de Tipo (Renta/Venta) --}}
                            @if($property->listing_type == 'rent')
                                <span class="bg-blue-100 text-blue-800 text-[11px] font-semibold px-3 py-1 rounded-full">Renta</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-[11px] font-semibold px-3 py-1 rounded-full">Venta</span>
                            @endif
                            {{-- Badge de Estatus (Disponible / No disponible / Rentada / Vendida) --}}
                            @php
                                $st = $property->status;
                                $statusClass = $badgeMap[$st] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-300';
                                $dotClass = $dotMap[$st] ?? 'bg-gray-400';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $statusClass }}">
                                <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
                                {{ $property->status_label }}
                            </span>
                            <p class="text-2xl font-bold text-blue-600 mt-2">${{ number_format($property->price, 2) }}</p>
                            {{-- (Opcional) Si está rentada y tienes fecha de término, muéstrala pequeñita: --}}
                            @php
                            // Busca una reserva vigente como fallback (end_date >= ahora, status pagado/confirmado)
                            $activeReservation = $property->reservations()
                                ->whereIn('status', ['paid','confirmed','completed'])  // ajusta a tus statuses reales
                                ->whereDate('end_date', '>=', now())
                                ->orderByDesc('end_date')
                                ->first();

                            $until = $property->rented_until ?? ($activeReservation?->end_date);
                        @endphp

                        @if($property->status === 'rented' && $until)
                            <p class="text-xs text-gray-500 mt-1">
                                Rentada hasta {{ \Illuminate\Support\Carbon::parse($until)->format('d/m/Y H:i') }}
                            </p>
                        @endif
                        </div>

                        {{-- Derecha: Botones de Acción (CORREGIDO) --}}
                        <div class="flex gap-2 flex-shrink-0 w-full md:w-auto justify-start md:justify-end">
                            {{-- Botón Editar --}}
                            <a href="{{ route('properties.edit', $property) }}"
                               class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-colors whitespace-nowrap font-medium text-sm">
                                <i class="fas fa-edit"></i>
                                <span>Editar</span>
                            </a>
                            {{-- BOTÓN TOGGLE STATUS (solo si no está sold/rented) --}}
                            @if(!in_array($property->status, ['sold','rented']))
                            @php $isAvail = $property->status === 'available'; @endphp
                            <button type="button"
                                    class="inline-flex items-center gap-2 {{ $isAvail ? 'bg-orange-100 hover:bg-orange-200 text-orange-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }} px-3 py-2 rounded-lg transition-colors whitespace-nowrap font-medium text-sm w-full md:w-auto justify-center prop-toggle-btn"
                                    data-url="{{ route('properties.toggleStatus', $property) }}"
                                    data-next="{{ $isAvail ? 'no disponible' : 'disponible' }}"
                                    data-title="{{ $property->title }}">
                                <i class="fas {{ $isAvail ? 'fa-ban' : 'fa-check' }}"></i>
                                <span>{{ $isAvail ? 'Marcar no disponible' : 'Marcar disponible' }}</span>
                            </button>
                            @endif
                            {{-- BOTÓN ELIMINAR (llama a JS para modal) --}}
                            <button type="button"
                                    onclick="openDeletePropertyModal(this)"
                                    data-property-name="{{ $property->title }}"
                                    data-delete-url="{{ route('properties.destroy', $property) }}"
                                    class="inline-flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg transition-colors whitespace-nowrap font-medium text-sm w-full md:w-auto justify-center">
                                <i class="fas fa-trash"></i>
                                <span>Eliminar</span>
                            </button>
                        </div>
                    </div>

                    {{-- Imágenes --}}
                    <div class="mb-5">
                       {{-- ... (código de imágenes sin cambios) ... --}}
                         <h4 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wide">Imágenes</h4>
                        @if ($property->images->isNotEmpty())
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                @foreach ($property->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="Imagen de {{ $property->title }}"
                                         class="w-full h-32 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No hay imágenes para esta propiedad.</p>
                        @endif
                    </div>

                    {{-- Amenidades --}}
                    <div>
                       {{-- ... (código de amenidades sin cambios) ... --}}
                         <h4 class="font-semibold text-gray-900 mb-3 text-sm uppercase tracking-wide">Amenidades</h4>
                        @if ($property->amenities->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($property->amenities as $amenity)
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                                        {{ $amenity->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No se especificaron amenidades.</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            {{-- Mensaje si no hay propiedades --}}
            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
               {{-- ... (código del mensaje @empty sin cambios) ... --}}
                 <i class="fas fa-home text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">
                     @if(request('type'))
                        No hay propiedades que coincidan con el filtro
                    @else
                        No tienes propiedades
                    @endif
                </h3>
                <p class="text-gray-500 mb-4">
                    @if(!request('type'))
                        Comienza añadiendo tu primera propiedad.
                    @endif
                </p>
                <a href="{{ route('properties.create') }}"
                   class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-5 py-3 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Añadir Propiedad</span>
                </a>
            </div>
            @endforelse
        </div>

         {{-- Paginación --}}
         @if ($properties->hasPages())
             <div class="mt-8">
                 {{ $properties->links() }}
             </div>
         @endif

    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center text-white font-bold text-xl mb-4">
                        <i class="fas fa-home mr-2"></i>
                        <span>SIN BECA NO HAY RENTA</span>
                    </div>
                    <p class="text-gray-300 mb-4">
                        Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades
                        con sus futuros dueños de manera eficiente y profesional.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-4">Navegación</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mi agenda</a></li>
                        <li><a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mapa</a></li>
                        <li><a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Panel de Agente</a></li>
                        <li><a href="{{ route('agent.view') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Modo Visitante</a></li>
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
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Privacidad</a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Términos</a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- ================= DELETE CONFIRMATION MODAL (PROPERTIES) ================= --}}
    <div id="deletePropertyConfirmModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md relative modal-content">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirmar Eliminación</h3>
            <p class="mb-6 text-gray-600 dark:text-gray-400">¿Estás seguro de que quieres eliminar la propiedad <strong id="deletePropertyName"></strong>? Esta acción no se puede deshacer y borrará sus imágenes asociadas.</p>

            <form id="deletePropertyConfirmForm" method="POST" action=""> {{-- Action set by JS --}}
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeDeletePropertyModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar Propiedad</button>
                </div>
            </form>
            <button onclick="closeDeletePropertyModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                &times;
             </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  // Reutilizable: estilo Tailwind para botones del Swal
  const swalOpts = {
    buttonsStyling: false,
    reverseButtons: true,
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonText: 'Sí, cambiar',
    customClass: {
      popup: 'rounded-xl',
      title: 'text-gray-900',
      htmlContainer: 'text-gray-700',
      actions: 'gap-3',
      confirmButton: 'px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none',
      cancelButton: 'px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 focus:outline-none',
    },
  };

  // ---- CAMBIAR ESTADO ----
  document.querySelectorAll('.prop-toggle-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const el   = e.currentTarget;
      const url  = el.dataset.url;
      const next = el.dataset.next;   // 'no disponible' | 'disponible'
      const name = el.dataset.title || 'la propiedad';

      const res = await Swal.fire({
        ...swalOpts,
        icon: 'question',
        title: 'Cambiar estado',
        html: `¿Seguro que quieres marcar <b>${name}</b> como <b>${next}</b>?`,
      });
      if (!res.isConfirmed) return;

      try {
        const resp = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: new URLSearchParams({ _method: 'PATCH' })
        });

        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        await Swal.fire({
          ...swalOpts,
          showCancelButton: false,
          icon: 'success',
          title: 'Actualizado',
          text: 'El estado se cambió correctamente.',
          confirmButtonText: 'Aceptar'
        });

        window.location.reload();
      } catch (err) {
        Swal.fire({
          ...swalOpts,
          showCancelButton: false,
          icon: 'error',
          title: 'Ups',
          text: 'No se pudo cambiar el estado. Intenta de nuevo.',
          confirmButtonText: 'Entendido'
        });
      }
    });
  });

  // ---- ELIMINAR CON EL MISMO DISEÑO ----
  document.querySelectorAll('[data-delete-url]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const name = btn.dataset.propertyName || 'la propiedad';
      const url  = btn.dataset.deleteUrl;

      const res = await Swal.fire({
        ...swalOpts,
        icon: 'warning',
        title: 'Eliminar propiedad',
        html: `¿Seguro que deseas eliminar <b>${name}</b>? Esta acción no se puede deshacer.`,
        confirmButtonText: 'Sí, eliminar',
      });
      if (!res.isConfirmed) return;

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

        await Swal.fire({
          ...swalOpts,
          showCancelButton: false,
          icon: 'success',
          title: 'Eliminada',
          text: 'La propiedad se eliminó correctamente.',
          confirmButtonText: 'Aceptar'
        });

        window.location.reload();
      } catch (err) {
        Swal.fire({
          ...swalOpts,
          showCancelButton: false,
          icon: 'error',
          title: 'Error',
          text: 'No se pudo eliminar la propiedad.',
          confirmButtonText: 'Entendido'
        });
      }
    });
  });
});
</script>



</body>
</html>
