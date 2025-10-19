<!DOCTYPE html>
<html>

<head>
    <title>Panel de Visitas - Agente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">


    <main class="max-w-7xl mx-auto mt-10 px-6">
        <h1 class="text-3xl font-bold mb-8">Panel de Visitas</h1>

        {{-- Sección 1: Confirmación de Citas Pendientes --}}
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Citas Pendientes de Confirmar</h2>

            @if($pendingVisits->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Propiedad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pendingVisits as $visit)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $visit->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $visit->client->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $visit->property->title }}</div>
                                <div class="text-sm text-gray-500">{{ $visit->property->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($visit->visit_date)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="confirmVisit({{ $visit->id }})"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md mr-2 transition">
                                    Confirmar
                                </button>
                                <button onclick="cancelVisit({{ $visit->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition">
                                    Cancelar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <p class="text-gray-500 text-lg">No tienes citas pendientes de confirmar.</p>
            </div>
            @endif
        </div>

        {{-- Sección 2: Agenda y Calendario --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Mi Agenda</h2>

            {{-- Filtros rápidos --}}
            <div class="mb-6 flex space-x-4">
                <button onclick="filterAgenda('all')" class="px-4 py-2 rounded-lg bg-blue-500 text-white">Todas</button>
                <button onclick="filterAgenda('today')"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">Hoy</button>
                <button onclick="filterAgenda('week')"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">Esta Semana</button>
                <button onclick="filterAgenda('confirmed')"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">Solo Confirmadas</button>
            </div>

            {{-- Lista de visitas agendadas --}}
            <div class="space-y-4">
                @if($allVisits->count() > 0)
                @foreach($allVisits as $visit)
                <div class="border-l-4 p-4 rounded-r-lg 
                        @if($visit->status == 'pending') border-yellow-500 bg-yellow-50
                        @else border-green-500 bg-green-50 @endif
                        agenda-item" data-date="{{ $visit->visit_date }}" data-status="{{ $visit->status }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="text-sm font-medium px-2 py-1 rounded 
                                        @if($visit->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                    @if($visit->status == 'pending') Pendiente
                                    @else Confirmada @endif
                                </span>
                                <span class="text-lg font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($visit->visit_date)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                                <div><strong>Cliente:</strong> {{ $visit->client->name }}</div>
                                <div><strong>Propiedad:</strong> {{ $visit->property->title }}</div>
                                <div><strong>Ubicación:</strong> {{ $visit->property->location }}</div>
                                <div><strong>Teléfono:</strong> {{ $visit->client->phone ?? 'No proporcionado' }}</div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($visit->status == 'pending')
                            <button onclick="confirmVisit({{ $visit->id }})"
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                Confirmar
                            </button>
                            @endif
                            <button onclick="viewProperty({{ $visit->property_id }})"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Ver Propiedad
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">No tienes visitas agendadas.</p>
                </div>
                @endif
            </div>

            {{-- Mini Calendario --}}
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Próximos 7 Días</h3>
                <div class="grid grid-cols-7 gap-2 text-center">
                    @php
                    $today = now();
                    $weekDates = [];
                    for ($i = 0; $i < 7; $i++) { $weekDates[]=$today->copy()->addDays($i);
                        }
                        @endphp

                        @foreach($weekDates as $date)
                        @php
                        $dayVisits = $allVisits->filter(function($visit) use ($date) {
                        return \Carbon\Carbon::parse($visit->visit_date)->isSameDay($date);
                        });
                        @endphp
                        <div class="p-3 border rounded-lg 
                        @if($date->isToday()) bg-blue-50 border-blue-200 @endif">
                            <div class="text-sm font-medium">
                                @php
                                $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                echo $dias[$date->dayOfWeek];
                                @endphp
                            </div>
                            <div class="text-lg font-bold">{{ $date->format('d') }}</div>
                            @if($dayVisits->count() > 0)
                            <div class="text-xs text-green-600 font-semibold">
                                {{ $dayVisits->count() }} visita(s)
                            </div>
                            @endif
                        </div>
                        @endforeach
                </div>
            </div>
        </div>
    </main>

    <script>
    function confirmVisit(visitId) {
        if (!confirm('¿Estás seguro de confirmar esta visita?')) {
            return;
        }

        fetch(`/visits/${visitId}/confirm`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Visita confirmada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al confirmar la visita');
            });
    }

    function cancelVisit(visitId) {
        if (!confirm('¿Estás seguro de cancelar esta visita?')) {
            return;
        }

        fetch(`/visits/${visitId}/cancel`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Visita cancelada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar la visita');
            });
    }

    function viewProperty(propertyId) {
        window.open(`/properties/${propertyId}`, '_blank');
    }

    function filterAgenda(filter) {
        const items = document.querySelectorAll('.agenda-item');
        const today = new Date().toDateString();
        const weekStart = new Date();
        weekStart.setDate(weekStart.getDate() - weekStart.getDay());

        items.forEach(item => {
            let show = true;
            const itemDate = new Date(item.dataset.date).toDateString();

            switch (filter) {
                case 'today':
                    show = itemDate === today;
                    break;
                case 'week':
                    const itemDateObj = new Date(item.dataset.date);
                    show = itemDateObj >= weekStart;
                    break;
                case 'confirmed':
                    show = item.dataset.status === 'confirmed';
                    break;
                default:
                    show = true;
            }

            item.style.display = show ? 'block' : 'none';
        });
    }
    </script>
</body>

</html>