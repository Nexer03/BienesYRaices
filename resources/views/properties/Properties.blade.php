<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mapa de Propiedades</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  #map { height: 80vh; width: 100%; border-radius: 1rem; }

  /* Marcadores estilo Airbnb */
  .price-marker {
    background-color: white;
    color: #222;
    padding: 6px 14px;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    border: 1px solid #ddd;
    cursor: pointer;
    transition: transform 0.2s, background-color 0.2s;
  }
  .price-marker:hover { transform: scale(1.1); background-color: #f8f8f8; }

  /* Ocultar POIs (hoteles, restaurantes, gasolineras, etc.) */
  .gm-style .gm-style-iw,
  .gm-style img[src*="spotlight-poi"],
  .gm-style div[style*="background-image"] {
    display: none !important;
  }

  /* Modal */
  .modal { display: flex; justify-content:center; align-items:center; position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.5); }
  .modal.hidden { display: none; }
  .modal-content { background:white; padding:1rem; border-radius:0.75rem; max-width:400px; width:90%; }
  .carousel-img { width:100%; height:200px; object-fit:cover; border-radius:0.5rem; }
</style>
</head>
<body class="bg-gray-50 font-sans">

<!-- Header estilo Airbnb -->
<header class="bg-white shadow-md p-6 flex flex-col md:flex-row justify-between items-center gap-4 rounded-b-lg">
  <h1 class="text-3xl font-bold text-gray-900">Explora propiedades</h1>
  <div class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
    <input type="search" placeholder="¿Dónde buscas?" class="border border-gray-300 rounded-full px-4 py-2 w-full md:w-64 focus:ring-2 focus:ring-red-500 focus:outline-none">
    <input type="search" placeholder="Rango de precio" class="border border-gray-300 rounded-full px-4 py-2 w-full md:w-40 focus:ring-2 focus:ring-red-500 focus:outline-none">
    <button class="px-4 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition">Buscar</button>
    <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition">Volver al inicio</a>
  </div>
</header>

<!-- Mapa -->
<main class="max-w-7xl mx-auto mt-6 rounded-2xl shadow-xl overflow-hidden">
  <div id="map"></div>
</main>

<!-- Footer estilo Airbnb -->
<footer class="bg-white mt-8 p-6 text-center text-gray-600 border-t shadow-inner">
  <p class="text-sm">Preguntas frecuentes | Soporte | Contacto</p>
</footer>

<!-- Modal Carrusel -->
<div id="modal" class="modal hidden">
  <div id="modalContent" class="modal-content">
    <button id="closeModal" class="mb-2 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">Cerrar</button>
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

// Cerrar modal
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

            // Modal al click
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
