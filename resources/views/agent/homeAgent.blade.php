<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Agente - SIN BECA NO HAY RENTA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <!-- Header simplificado -->
    <x-main-header />

    <!-- Contenido principal -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Sección de bienvenida -->
        <section class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Bienvenido a la vista de agente
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Gestiona tus propiedades, agendas y comunicaciones desde un solo lugar
            </p>
        </section>

        <!-- Botón Crear Nueva Propiedad -->
        <div class="text-center mb-12">
            <a href="{{ route('properties.create') }}"
               class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg px-6 py-3 transition-colors duration-200 shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Crear Nueva Propiedad</span>
            </a>
        </div>

        <!-- Grid de acciones -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <!-- Mis Propiedades -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Mis Propiedades</h3>
                <p class="text-gray-600 mb-4">Gestiona y visualiza todas tus propiedades listadas</p>
                <a href="{{ route('properties.my') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver propiedades →
                </a>
            </div>

            <!-- Agendas -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Agendas</h3>
                <p class="text-gray-600 mb-4">Administra tus citas y visitas programadas</p>
                <a href="{{ route('agent.visits.index') }}"
                   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                    Ver agenda →
                </a>
            </div>

            <!-- Mensajería -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
                <div class="text-blue-500 text-3xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Mensajería</h3>
                <p class="text-gray-600 mb-4">Comunícate con clientes y prospectos</p>
              <a href="{{ route('chat.index') }}"
   class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
  Abrir mensajería →
</a>
            </div>
            <!-- Estadísticas -->
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 p-6 text-center">
            <div class="text-blue-500 text-3xl mb-4 relative inline-block">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Estadísticas</h3>
            <p class="text-gray-600 mb-4">Resumen de propiedades y visitas en el tiempo</p>
            <a href="{{ route('agent.analytics') }}"
                class="inline-block text-blue-500 hover:text-blue-600 font-medium transition-colors duration-200">
                Ver estadísticas →
            </a>
            </div>

        </div>
    </main>

    <!-- Footer -->
   <x-main-footer />

</body>
</html>
