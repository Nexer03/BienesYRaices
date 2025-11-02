<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Propiedades - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mantenemos #map al 80vh para que ocupe gran parte de la pantalla */
        #map { height: 80vh; width: 100%; }

        /* Estilos para el marcador de precio (del old) */
        .price-marker {
            background-color: white;
            color: #222;
            padding: 6px 14px;
            border-radius: 9999px; /* Tailwind: rounded-full */
            font-weight: 600; /* Tailwind: font-semibold */
            font-size: 14px; /* Tailwind: text-sm */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); /* Tailwind: shadow-lg */
            border: 1px solid #ddd; /* Tailwind: border border-gray-300 */
            cursor: pointer;
            transition: transform 0.2s;
        }
        .price-marker:hover { transform: scale(1.1); }

        /* Ocultar POIs (del old) */
        .gm-style .gm-style-iw,
        .gm-style img[src*="spotlight-poi"],
        .gm-style div[style*="background-image"] {
            display: none !important;
        }

        /* Estilos del modal (del old, adaptados a Tailwind) */
        .modal { display: flex; justify-content:center; align-items:center; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.6); transition: opacity 0.25s ease; }
        .modal.hidden { opacity: 0; pointer-events: none; }
        .modal-content { background:white; padding:1.5rem; border-radius:0.75rem; max-width:450px; width:90%; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transform: scale(0.95); transition: transform 0.25s ease; }
        .modal:not(.hidden) .modal-content { transform: scale(1); } /* Animación al abrir */
        .carousel-container { display: flex; overflow-x: auto; gap: 0.5rem; scroll-snap-type: x mandatory; }
        .carousel-img { width:100%; height:200px; object-fit:cover; border-radius:0.5rem; scroll-snap-align: center; flex-shrink: 0; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- Header (del nuevo diseño) --}}
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            {{-- Logo --}}
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                <a href="{{ route('home') }}"><i class="fas fa-home mr-2"></i>Sin beca<span class="text-gray-700"> no hay renta </span></a>
            </div>

            {{-- Filtros en el Medio (del nuevo diseño) --}}
           <div class="flex items-center space-x-3 flex-1 max-w-2xl mx-8">
                <input type="number" id="minPrice" placeholder="Precio min" min="0" class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="number" id="maxPrice" placeholder="Precio máx"  min="0" class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                
                <select id="listingType" class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Todos</option>
                    <option value="sale">Venta</option>
                    <option value="rent">Renta</option>
                </select>

                <button id="filterBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-medium">
                    Filtrar
                </button>
            </div>


            {{-- Navegación (del nuevo diseño, ajustada) --}}
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Inicio</a>
                {{-- Mantenemos el perfil si está autenticado --}}
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
                @else
                    <button type="button" onclick="openLoginModal()" {{-- Asume que tienes modal login en welcome --}}
                        class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Iniciar sesión</button>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Contenido Principal --}}
    <main class="flex-grow">
        {{-- Mapa (ocupa todo el espacio disponible) --}}
        <section class="w-full h-full"> {{-- Ajustado para ocupar espacio --}}
            <div id="map"></div>
        </section>
    </main>

    {{-- Footer (del nuevo diseño) --}}
    {{-- Se elimina el footer para dar más espacio al mapa, o puedes mantenerlo si prefieres --}}
    {{-- <footer class="bg-gray-800 text-white py-8"> ... </footer> --}}

    {{-- Modal Carrusel (del old, con estilos Tailwind mejorados) --}}
    <div id="modal" class="modal hidden">
        <div id="modalContent" class="modal-content">
            {{-- Botón Cerrar (Mejorado) --}}
            <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl leading-none">&times;</button>

            {{-- Contenido del Modal --}}
            <div id="modalInfo" class="text-gray-800 mb-4">
                {{-- El título y descripción se llenarán con JS --}}
            </div>
            {{-- Carrusel --}}
            <div id="carousel" class="carousel-container scrollbar-hide mb-2">
                {{-- Las imágenes se llenarán con JS --}}
            </div>
        </div>
    </div>

    {{-- Script (Combinado con filtrado de marcadores) --}}
    <script>
        // Datos de propiedades pasados desde el controlador
        const properties = @json($properties);

        // Elementos del Modal
        const modal = document.getElementById('modal');
        const carousel = document.getElementById('carousel');
        const modalInfo = document.getElementById('modalInfo');
        const closeModalBtn = document.getElementById('closeModal');

        // Variables del mapa y marcadores
        let map;
        let markers = [];

        // Evento para cerrar modal
        if(closeModalBtn) {
            closeModalBtn.addEventListener('click', closePropertyModal);
        }
        // Cerrar al hacer clic fuera
        if(modal) {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closePropertyModal();
                }
            });
        }

        function closePropertyModal() {
            if(modal) modal.classList.add('hidden');
            if(carousel) carousel.innerHTML = ''; // Limpiar carrusel
            if(modalInfo) modalInfo.innerHTML = ''; // Limpiar info
        }

        // Carga de Google Maps API
        fetch('/maps-key')
        .then(res => res.json())
        .then(data => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap&libraries=places`;
            script.async = true;
            document.head.appendChild(script);
        });

        // Inicialización del Mapa
        function initMap() {
            const defaultLocation = { lat: 20.749757, lng: -105.258849 };
            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 12,
                styles: [
                    { featureType: "poi.business", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.park", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.school", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                    { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
                ],
                mapTypeControl: false,
                streetViewControl: false
            });

            // Crear todos los marcadores y guardarlos en array
            properties.forEach(prop => {
                if (prop.latitude && prop.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(prop.latitude), lng: parseFloat(prop.longitude) },
                        map: map,
                        label: {
                            text: `$${Number(prop.price).toLocaleString('es-MX')}`,
                            className: 'price-marker'
                        },
                        icon: ' ',
                        title: prop.title,
                        listingType: prop.listing_type, // "sale" o "rent"
                        price: prop.price
                    });

                    marker.addListener('click', () => openPropertyModal(prop));

                    markers.push(marker);
                }
            });

            // Botón de filtrado
            const filterBtn = document.getElementById('filterBtn');
            if(filterBtn) filterBtn.addEventListener('click', filterMarkers);
        }

        // Función para filtrar marcadores según precio y tipo
        function filterMarkers() {
    let minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    let maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;

    // Evitar precios negativos
    minPrice = Math.max(0, minPrice);
    maxPrice = Math.max(0, maxPrice);

    const listingType = document.getElementById('listingType').value;

    markers.forEach(marker => {
        const matchPrice = marker.price >= minPrice && marker.price <= maxPrice;
        const matchType = listingType === '' || marker.listingType === listingType;
        marker.setMap(matchPrice && matchType ? map : null);
    });
}


        // Función para abrir modal con info y carrusel de imágenes
        function openPropertyModal(prop) {
            carousel.innerHTML = '';
            modalInfo.innerHTML = '';

            modalInfo.innerHTML = `
                <h3 class="font-semibold text-lg truncate mb-1">${prop.title}</h3>
                <p class="text-sm text-gray-600 truncate mb-2">${prop.location ?? ''}</p>
                <p class="text-lg font-bold text-blue-600">$${Number(prop.price).toLocaleString('es-MX')}</p>
                <a href="/properties/${prop.id}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">Ver detalles</a>
            `;

            const images = prop.images && prop.images.length ? prop.images : [];
            if(images.length > 0) {
                images.forEach(imgData => {
                    const imgEl = document.createElement('img');
                    imgEl.src = `{{ asset('storage') }}/${imgData.image_path}`;
                    imgEl.alt = prop.title;
                    imgEl.className = 'carousel-img';
                    carousel.appendChild(imgEl);
                });
            } else {
                const imgEl = document.createElement('img');
                imgEl.src = 'https://via.placeholder.com/400x200?text=Sin+Imagen';
                imgEl.alt = 'Sin imagen';
                imgEl.className = 'carousel-img';
                carousel.appendChild(imgEl);
            }

            modal.classList.remove('hidden');
        }
    </script>


</body>
</html>
