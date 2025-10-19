<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Visitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    
    <main class="max-w-4xl mx-auto mt-10 px-6">
        <h1 class="text-3xl font-bold mb-6">Todas Mis Visitas</h1>
        
        {{-- Filtros por estado --}}
        <div class="mb-6 flex space-x-4">
            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" 
               class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Todas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg {{ request('status') == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Pendientes
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'confirmed']) }}" 
               class="px-4 py-2 rounded-lg {{ request('status') == 'confirmed' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Confirmadas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}" 
               class="px-4 py-2 rounded-lg {{ request('status') == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Completadas
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}" 
               class="px-4 py-2 rounded-lg {{ request('status') == 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                Canceladas
            </a>
        </div>
        
        @if($visits->count() > 0)
            <div class="space-y-4">
                @foreach($visits as $visit)
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 
                    @if($visit->status == 'pending') border-yellow-500
                    @elseif($visit->status == 'confirmed') border-green-500
                    @elseif($visit->status == 'completed') border-blue-500
                    @elseif($visit->status == 'cancelled') border-red-500
                    @endif">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-semibold">{{ $visit->property->title }}</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($visit->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($visit->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($visit->status == 'completed') bg-blue-100 text-blue-800
                                    @elseif($visit->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    @if($visit->status == 'pending') Pendiente
                                    @elseif($visit->status == 'confirmed') Confirmada
                                    @elseif($visit->status == 'completed') Completada
                                    @elseif($visit->status == 'cancelled') Cancelada
                                    @endif
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">{{ $visit->property->location }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                                <div>
                                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($visit->visit_date)->format('d/m/Y H:i') }}
                                </div>
                                <div>
                                    <strong>Agente:</strong> {{ $visit->agent->name }}
                                </div>
                                <div>
                                    <strong>Precio:</strong> ${{ number_format($visit->property->price, 2) }}
                                </div>
                                <div>
                                    <strong>Tipo:</strong> 
                                    @if($visit->property->listing_type == 'rent') Renta
                                    @else Venta
                                    @endif
                                </div>
                            </div>
                            @if($visit->notes)
                                <div class="mt-3 p-3 bg-gray-50 rounded">
                                    <strong>Notas:</strong> {{ $visit->notes }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <p class="text-gray-500 text-lg">No tienes visitas agendadas.</p>
                <a href="/" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Explorar propiedades</a>
            </div>
        @endif
    </main>
</body>
</html>