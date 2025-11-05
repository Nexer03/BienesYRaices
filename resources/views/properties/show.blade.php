<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->title }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

<main class="max-w-4xl mx-auto mt-10 px-6">

    <div class="mb-4">
        <h1 class="text-3xl font-bold">{{ $property->title }}</h1>
        <p class="text-md text-gray-600 mt-1">{{ $property->location }}</p>
    </div>

    <div class="mb-6">
        @if ($property->images->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 rounded-lg overflow-hidden" style="max-height: 500px;">
            {{-- Imagen principal --}}
            <a href="{{ asset('storage/' . $property->images->first()->image_path) }}" data-lightbox="property-gallery"
               data-title="{{ $property->title }}" class="col-span-2 row-span-2 relative group">
                <img src="{{ asset('storage/' . $property->images->first()->image_path) }}"
                     alt="Imagen principal de {{ $property->title }}"
                     class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </a>

            {{-- Imágenes secundarias --}}
            @foreach ($property->images->slice(1)->take(3) as $image)
            <a href="{{ asset('storage/' . $image->image_path) }}" data-lightbox="property-gallery"
               data-title="{{ $property->title }}" class="relative group">
                <img src="{{ asset('storage/' . $image->image_path) }}"
                     alt="Imagen de {{ $property->title }}"
                     class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </a>
            @endforeach

            {{-- Quinta imagen con overlay --}}
            @if ($property->images->count() >= 5)
            @php $fifthImage = $property->images->slice(4)->first(); @endphp
            <div class="relative group cursor-pointer"
                 onclick="document.querySelector('[data-lightbox=\'property-gallery\']').click();">
                <img src="{{ asset('storage/' . $fifthImage->image_path) }}"
                     alt="Ver más imágenes de {{ $property->title }}"
                     class="w-full h-full object-cover filter grayscale hover:filter-none transition-all duration-300">
                <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white text-lg font-semibold opacity-100 group-hover:opacity-0 transition-opacity duration-300">
                    <span>+ {{ $property->images->count() - 4 }} fotos</span>
                </div>
            </div>
            @endif

            {{-- Resto de imágenes ocultas --}}
            @if ($property->images->count() > 5)
            @foreach ($property->images->slice(5) as $image)
            <a href="{{ asset('storage/' . $image->image_path) }}" data-lightbox="property-gallery"
               data-title="{{ $property->title }}" class="hidden"></a>
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
                @if($property->bedrooms)
                <span>&#128719;️ {{ $property->bedrooms }} Habitaciones</span>
                @endif
                @if($property->bathrooms)
                <span>&#128705; {{ $property->bathrooms }} Baños</span>
                @endif
            </div>

            <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Descripción</h2>
            <p class="text-gray-700 leading-relaxed">
                {{ $property->description ?? 'No hay descripción disponible.' }}
            </p>

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
            @endforelse
        </div>

        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md border sticky top-28">
                <p class="text-2xl font-bold">${{ number_format($property->price, 2) }}</p>
                @if($property->listing_type == 'rent')
                  <p class="text-gray-500">Precio de renta</p>
                @elseif($property->listing_type == 'sale')
                  <p class="text-gray-500">Precio de venta</p>
                @endif

                {{-- ================= Acciones por tipo de listado ================= --}}
                @if($property->listing_type == 'sale')
                    {{-- SOLO VENTA: mostrar botón "Contactar con el agente" --}}
                    <button type="button"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg mt-4 hover:bg-blue-700 transition font-semibold">
                        Contactar con el agente
                    </button>
                @elseif($property->listing_type == 'rent')
                    {{-- SOLO RENTA: mostrar "Reservar" y ocultar "Agendar visita" --}}
                    <button id="reserveButton"
                            class="w-full bg-blue-500 text-white py-3 rounded-lg mt-4 hover:bg-blue-600 transition font-semibold">
                        Reservar
                    </button>
                @endif
                {{-- ================================================================ --}}
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-2xl font-semibold border-b pb-2 mb-4">Ubicación</h2>
        <div id="map"></div>
    </div>
</main>

{{-- Modal de visita (se mantiene por si luego lo activamos, pero la UI ya no lo muestra en renta/venta) --}}
<div id="visitModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background: rgba(0,0,0,0.5);z-index:1000;">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border-radius:8px;min-width:300px;">
        <h3 class="text-lg font-semibold mb-4">Selecciona fecha y hora para tu visita</h3>
        <input type="datetime-local" id="visitDateTime" class="w-full p-2 border rounded mb-4">
        <div class="flex gap-2">
            <button onclick="scheduleVisit()"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Agendar Visita</button>
            <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</button>
        </div>
    </div>
</div>

{{-- Modal de reserva para rent --}}
<div id="reservationModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background: rgba(0,0,0,0.5);z-index:1000;">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border-radius:8px;min-width:350px;">
        <h3 class="text-lg font-semibold mb-4">Selecciona fechas de entrada y salida</h3>
        <label>Entrada:</label>
        <input type="date" id="checkInDate" class="w-full p-2 border rounded mb-2" min="{{ date('Y-m-d') }}">
        <label>Salida:</label>
        <input type="date" id="checkOutDate" class="w-full p-2 border rounded mb-4" min="{{ date('Y-m-d') }}">

        <div id="priceSummary" class="mb-4 p-3 bg-gray-100 rounded hidden">
            <p class="font-semibold">Resumen de reserva:</p>
            <p id="nightsCount">Noches: 0</p>
            <p id="totalPrice">Total: $0.00 MXN</p>
        </div>

        <div class="flex flex-col gap-3">
            <button onclick="submitReservation()"
                    class="bg-green-500 text-white px-4 py-3 rounded hover:bg-green-600">Confirmar Reserva</button>
            <button onclick="closeReservationModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</button>
        </div>
    </div>
</div>

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
                    <a href="#" class="text-gray-300 hover:text-white transition-colors duración-200"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-300 hover:text-white transition-colors duración-200"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-4">Navegación</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mis Visitas</a></li>
                    <li><a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Mapa</a></li>
                    <li><a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Panel de Agente</a></li>
                    <li><a href="{{ route('agent.view') }}" class="text-gray-300 hover:text-white transition-colors duration-200">Modo Vendedor</a></li>
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

<script>
    const propertyLocation = { lat: {{ $property->latitude }}, lng: {{ $property->longitude }} };
    const propertyId = {{ $property->id }};
    const propertyPrice = {{ $property->price }};
    let reservationData = null;

    // Mapas
    fetch('/maps-key')
        .then(res => res.json())
        .then(data => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
            script.async = true;
            document.head.appendChild(script);
        });

    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), { center: propertyLocation, zoom: 16 });
        new google.maps.Marker({ map: map, position: propertyLocation, title: "{{ $property->title }}" });
    }

    // Modales de visita (no visibles en UI actual, quedan por si los activamos luego)
    function openVisitCalendar() {
        const now = new Date();
        const defaultDate = new Date();
        defaultDate.setDate(now.getDate() + 2);
        defaultDate.setHours(10,0,0,0);
        const dateTimeInput = document.getElementById('visitDateTime');
        dateTimeInput.min = now.toISOString().slice(0,16);
        dateTimeInput.value = defaultDate.toISOString().slice(0,16);
        document.getElementById('visitModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('visitModal').style.display = 'none';
    }

    function scheduleVisit() {
        const visitDate = document.getElementById('visitDateTime').value;
        if (!visitDate) { alert('Por favor selecciona una fecha y hora'); return; }

        fetch('/visits', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ property_id: propertyId, agent_id: {{ $property->user_id }}, visit_date: visitDate })
        }).then(res => res.json())
          .then(data => {
            if (data.success) { alert('Visita agendada'); closeModal(); }
            else { alert('Error: ' + data.message); }
          }).catch(err => { console.error(err); alert('Error al agendar la visita'); });
    }

    // Modales de reserva
    function openReservation() {
        document.getElementById('reservationModal').style.display = 'block';
        document.getElementById('checkInDate').value = '';
        document.getElementById('checkOutDate').value = '';
        document.getElementById('priceSummary').classList.add('hidden');
        reservationData = null;
    }

    function closeReservationModal() {
        document.getElementById('reservationModal').style.display = 'none';
    }

    function calculatePrice() {
        const checkIn = document.getElementById('checkInDate').value;
        const checkOut = document.getElementById('checkOutDate').value;
        if(checkIn && checkOut){
            const nights = Math.ceil((new Date(checkOut)-new Date(checkIn))/(1000*60*60*24));
            const totalPrice = (propertyPrice*nights).toFixed(2);
            document.getElementById('nightsCount').textContent = 'Noches: '+nights;
            document.getElementById('totalPrice').textContent = 'Total: $'+totalPrice+' MXN';
            document.getElementById('priceSummary').classList.remove('hidden');
            reservationData = { property_id: propertyId, start_date: checkIn, end_date: checkOut, nights, total_price: totalPrice };
        }
    }

    function submitReservation() {
        if(!reservationData){ alert('Selecciona fechas primero'); return; }

        fetch('/reservations', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            body: JSON.stringify(reservationData)
        }).then(res=>res.json())
          .then(data=>{
            if(data.success){
                alert('Reserva creada con éxito. ID: '+data.reservation_id);
                closeReservationModal();
            } else alert('Error al crear reserva: '+data.message);
          }).catch(err=>{ console.error(err); alert('Error al crear reserva'); });
    }

    document.getElementById('checkInDate').addEventListener('change', function(){
        const minCheckOut = new Date(this.value);
        minCheckOut.setDate(minCheckOut.getDate()+1);
        document.getElementById('checkOutDate').min = minCheckOut.toISOString().split('T')[0];
        if(document.getElementById('checkOutDate').value <= this.value)
            document.getElementById('checkOutDate').value='';
        calculatePrice();
    });
    document.getElementById('checkOutDate').addEventListener('change', calculatePrice);

    document.getElementById('reserveButton')?.addEventListener('click', openReservation);
    window.onclick = function(event){
        if(event.target===document.getElementById('visitModal')) closeModal();
        if(event.target===document.getElementById('reservationModal')) closeReservationModal();
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

</body>
</html>
