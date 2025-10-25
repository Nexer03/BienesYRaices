<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Propiedades - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #map { height: 80vh; width: 100%; }

        .price-marker {
            background-color: white;
            color: #222;
            padding: 6px 14px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .price-marker:hover { transform: scale(1.1); }

        .gm-style .gm-style-iw,
        .gm-style img[src*="spotlight-poi"],
        .gm-style div[style*="background-image"] {
            display: none !important;
        }

        .modal { display: flex; justify-content:center; align-items:center; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.5); }
        .modal.hidden { display: none; }
        .modal-content { background:white; padding:1rem; border-radius:0.75rem; max-width:400px; width:90%; }
        .carousel-img { width:100%; height:200px; object-fit:cover; border-radius:0.5rem; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Header con Filtros en el Medio -->
    <header class="sticky top-0 bg-white shadow-sm z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
            <!-- Logo -->
            <div class="text-2xl font-bold text-blue-600 cursor-pointer">
                <i class="fas fa-home mr-2"></i>
                Sin beca<span class="text-gray-700"> no hay renta </span>
            </div>

            <!-- Filtros en el Medio -->
            <div class="flex items-center space-x-3 flex-1 max-w-2xl mx-8">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" 
                           placeholder="¿Dónde buscas?" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                
                <input type="number" 
                       placeholder="Precio máx"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition font-medium">
                    Buscar
                </button>
            </div>

            <!-- Navegación -->
            <nav class="flex items-center space-x-6 text-gray-700 font-medium">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Inicio</a>
                <a href="{{ url('/dashboard') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition">Perfil</a>
            </nav>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-grow">
        <!-- Mapa -->
        <section class="w-full">
            <div id="map"></div>
        </section>
    </main>

    <!-- Footer Estandarizado -->
    <footer class="bg-gray-800 text-white py-8">
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
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Navegación</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Inicio
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('properties.map') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mapa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('agent.home') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Panel de Agente
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('visits.my') }}" class="text-gray-300 hover:text-white transition-colors duration-200">
                                Mis Visitas
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            soporte@sinbeca.com
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            +1 (555) 123-4567
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Ciudad, País
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-300 text-sm">
                        &copy; 2024 SIN BECA NO HAY RENTA. Todos los derechos reservados.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Privacidad
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Términos
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white text-sm transition-colors duration-200">
                            Cookies
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Carrusel -->
    <div id="modal" class="modal hidden">
        <div id="modalContent" class="modal-content">
            <button id="closeModal" class="mb-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Cerrar</button>
            <div id="carousel" class="flex overflow-x-scroll gap-2 scrollbar-hide mb-2"></div>
            <div id="modalInfo" class="text-gray-800"></div>
        </div>
    </div>

    <script>
        const properties = @json($properties);
        const modal = document.getElementById('modal');
        const carousel = document.getElementById('carousel');
        const modalInfo = document.getElementById('modalInfo');
        const closeModal = document.getElementById('closeModal');

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
            carousel.innerHTML = '';
            modalInfo.innerHTML = '';
        });

        fetch('/maps-key')
        .then(res => res.json())
        .then(data => {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${data.key}&callback=initMap`;
            script.async = true;
            document.head.appendChild(script);
        });

        function initMap() {
            const defaultLocation = { lat: 20.749757, lng: -105.258849 };
            const map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 14,
                styles: [
                    { featureType: "poi.business", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.park", stylers: [{ visibility: "off" }] },
                    { featureType: "poi.school", stylers: [{ visibility: "off" }] },
                    { featureType: "transit", stylers: [{ visibility: "off" }] },
                    { featureType: "road", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
                ]
            });

            properties.forEach(prop => {
                if (prop.latitude && prop.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(prop.latitude), lng: parseFloat(prop.longitude) },
                        map: map,
                        label: {
                            text: `$${prop.price}`,
                            className: 'price-marker'
                        },
                        title: prop.title
                    });

                    marker.addListener('click', () => {
                        carousel.innerHTML = '';
                        modalInfo.innerHTML = '';
                        const images = prop.images && prop.images.length ? prop.images : [
                            '/placeholder1.jpg', '/placeholder2.jpg', '/placeholder3.jpg'
                        ];
                        images.forEach(img => {
                            const imgEl = document.createElement('img');
                            imgEl.src = img;
                            imgEl.className = 'carousel-img';
                            carousel.appendChild(imgEl);
                        });

                        modalInfo.innerHTML = `
                            <strong class="block text-lg font-semibold">${prop.title}</strong>
                            <p class="text-sm text-gray-600 mt-2">${prop.description}</p>
                            <p class="text-sm text-gray-800 mt-3 font-medium">Precio: $${prop.price}</p>
                        `;

                        modal.classList.remove('hidden');
                    });
                }
            });
        }
    </script>

</body>
</html>