<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro como Agente</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

  {{-- HEADER --}}
  <header class="bg-white shadow fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <a href="{{ route('home') }}" class="flex items-center text-2xl font-bold text-blue-600 hover:text-blue-700">
        <i class="fas fa-home mr-2"></i> Sin beca <span class="text-gray-700 ml-1">no hay renta</span>
      </a>
    </div>
  </header>

  {{-- CONTENIDO PRINCIPAL --}}
  <main class="flex-1 flex items-center justify-center mt-24 px-4">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg">
      <div class="text-center mb-6">
        <i class="fas fa-user-tie text-4xl text-blue-600 mb-2"></i>
        <h1 class="text-3xl font-bold text-gray-800">Registro como Agente</h1>
        <p class="text-gray-500 mt-2">Completa los siguientes campos para enviar tu solicitud</p>
      </div>

      <form action="{{ route('agent.register.store') }}" method="POST" id="agentForm" class="space-y-6">
        @csrf

        {{-- RFC --}}
        <div>
          <label for="rfc" class="block font-medium text-gray-700 mb-1">
            <i class="fa-solid fa-id-card mr-2 text-blue-500"></i>RFC
          </label>
          <input type="text" name="rfc" id="rfc" placeholder="AAA000000AAA"
            pattern="[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 uppercase">
          <small class="text-gray-500 text-sm">Formato: 3–4 letras, 6 números, 3 caracteres</small>
        </div>

        {{-- CURP --}}
        <div>
          <label for="curp" class="block font-medium text-gray-700 mb-1">
            <i class="fa-solid fa-user-check mr-2 text-blue-500"></i>CURP
          </label>
          <input type="text" name="curp" id="curp" placeholder="AAAA000000HAAAAAA00"
            pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}" required
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 uppercase">
          <small class="text-gray-500 text-sm">Formato: 4 letras, 6 números, 1 letra (H/M), 5 letras, 2 caracteres</small>
        </div>

        <button type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
          <i class="fa-solid fa-paper-plane mr-2"></i>Enviar Solicitud
        </button>
      </form>
    </div>
  </main>

  {{-- FOOTER --}}
  <footer class="bg-gray-800 text-white py-6 mt-auto">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
      <p class="text-sm text-gray-300">&copy; {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
      <div class="flex space-x-4 mt-3 md:mt-0">
        <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="hover:text-blue-400"><i class="fab fa-twitter"></i></a>
        <a href="#" class="hover:text-blue-400"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </footer>

  {{-- VALIDACIONES --}}
  <script>
    const rfcInput = document.getElementById('rfc');
    const curpInput = document.getElementById('curp');

    rfcInput.addEventListener('input', e => {
      e.target.value = e.target.value.toUpperCase();
      const pattern = /^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
      e.target.style.borderColor = (e.target.value && !pattern.test(e.target.value)) ? 'red' : '';
    });

    curpInput.addEventListener('input', e => {
      e.target.value = e.target.value.toUpperCase();
      const pattern = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z]{2}$/;
      e.target.style.borderColor = (e.target.value && !pattern.test(e.target.value)) ? 'red' : '';
    });
  </script>
</body>
</html>
