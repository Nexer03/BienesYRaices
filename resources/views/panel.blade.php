<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Administrativo') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Cards de métricas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-gray-500 text-sm">Propiedades Vendidas</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalValueSold ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-gray-500 text-sm">Propiedades Rentadas</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalValueRented ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-gray-500 text-sm">Total Agentes</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $salesByAgent->count() ?? 0 }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-gray-500 text-sm">Ingresos Totales</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                    ${{ number_format(($totalValueSold ?? 0) + ($totalValueRented ?? 0), 2) }}
                </p>
            </div>
        </div>

        {{-- Tabla de resumen por agente --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Resumen por Agente</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                            <th class="px-4 py-2">Agente</th>
                            <th class="px-4 py-2">Propiedades Vendidas</th>
                            <th class="px-4 py-2">Propiedades Rentadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByAgent as $agent)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2">{{ $agent->name }}</td>
                            <td class="px-4 py-2">{{ $agent->properties_count }}</td>
                            <td class="px-4 py-2">
                                {{ $rentalsByAgent->firstWhere('id', $agent->id)->properties_count ?? 0 }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Gráficas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Ventas por Agente</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Rentas por Agente</h3>
                <canvas id="rentalsChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesData = {
            labels: @json($salesByAgent->pluck('name')),
            datasets: [{
                label: 'Propiedades Vendidas',
                data: @json($salesByAgent->pluck('properties_count')),
                backgroundColor: 'rgba(59, 130, 246, 0.7)'
            }]
        };

        const rentalsData = {
            labels: @json($rentalsByAgent->pluck('name')),
            datasets: [{
                label: 'Propiedades Rentadas',
                data: @json($rentalsByAgent->pluck('properties_count')),
                backgroundColor: 'rgba(16, 185, 129, 0.7)'
            }]
        };

        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: salesData,
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('rentalsChart'), {
            type: 'bar',
            data: rentalsData,
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    </script>

</x-app-layout>
