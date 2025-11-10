<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reporte de Ventas y Rentas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- ===== Toolbar de filtros (compacta y limpia) ===== --}}
@php
    $from   = $filters['from'] ? $filters['from']->format('Y-m-d') : '';
    $to     = $filters['to']   ? $filters['to']->format('Y-m-d')   : '';
    $agent  = $filters['agent'] ?? '';
    $city   = $filters['city']  ?? '';

    // Estilo base para controles (sin plugin forms)
    $control = 'w-full appearance-none rounded-lg border border-gray-300/80 dark:border-gray-700 bg-white dark:bg-gray-900/60
                text-sm text-gray-800 dark:text-gray-100 px-3 py-2 shadow-sm
                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
    $label   = 'text-[11px] font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300';
    $btn     = 'inline-flex items-center justify-center rounded-lg px-3.5 py-2 text-sm font-semibold shadow-sm focus:outline-none';
@endphp

<div class="mb-6 rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Filtros</h3>

        {{-- Export (a la derecha, sin icono gigante) --}}
        <form method="GET" action="{{ route('admin.reports.properties.export') }}" class="hidden md:block">
            <input type="hidden" name="from"  value="{{ $from }}">
            <input type="hidden" name="to"    value="{{ $to }}">
            <input type="hidden" name="agent" value="{{ $agent }}">
            <input type="hidden" name="city"  value="{{ $city }}">
            <button type="submit" class="{{ $btn }} bg-emerald-600 text-white hover:bg-emerald-500 focus:ring-2 focus:ring-emerald-500">
                Exportar comparativa en Excel
            </button>
        </form>
    </div>

    <div class="px-4 py-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-3">
                <label class="{{ $label }}">Desde</label>
                <input type="date" name="from" value="{{ $from }}" class="{{ $control }}">
            </div>

            <div class="md:col-span-3">
                <label class="{{ $label }}">Hasta</label>
                <input type="date" name="to" value="{{ $to }}" class="{{ $control }}">
            </div>

            <div class="md:col-span-3">
                <label class="{{ $label }}">Agente</label>
                <select name="agent" class="{{ $control }}">
                    <option value="">Todos</option>
                    @foreach($agentsOptions as $opt)
                        <option value="{{ $opt->id }}" @selected($agent == $opt->id)>{{ $opt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="{{ $label }}">Ciudad</label>
                <select name="city" class="{{ $control }}">
                    <option value="">Todas</option>
                    @foreach($citiesOptions as $c)
                        <option value="{{ $c }}" @selected($city === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-12 flex items-center gap-2">
                <button type="submit" class="{{ $btn }} bg-indigo-600 text-white hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    Aplicar
                </button>
                <a href="{{ route('admin.reports.sales') }}"
                   class="{{ $btn }} bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600">
                    Limpiar
                </a>

                {{-- Export visible también en móvil --}}
                <form method="GET" action="{{ route('admin.reports.properties.export') }}" class="md:hidden ml-auto">
                    <input type="hidden" name="from"  value="{{ $from }}">
                    <input type="hidden" name="to"    value="{{ $to }}">
                    <input type="hidden" name="agent" value="{{ $agent }}">
                    <input type="hidden" name="city"  value="{{ $city }}">
                    <button type="submit" class="{{ $btn }} bg-emerald-600 text-white hover:bg-emerald-500 focus:ring-2 focus:ring-emerald-500">
                        Exportar
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

                    {{-- ===== KPIs (igual que la versión anterior) ===== --}}
                    @php
                        $kpiCard  = 'p-4 bg-gray-50 dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700';
                        $kpiTitle = 'text-[11px] uppercase tracking-wide font-medium text-gray-500 dark:text-gray-400';
                        $kpiVal   = 'mt-2 text-2xl font-bold tracking-tight';
                    @endphp

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Propiedades vendidas</p>
                            <p class="{{ $kpiVal }}">{{ number_format($salesByAgent->sum('properties_count')) }}</p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Valor total vendido</p>
                            <p class="{{ $kpiVal }} text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($totalValueSold, 2, '.', ',') }}
                            </p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Reservas confirmadas</p>
                            <p class="{{ $kpiVal }}">{{ number_format($totalRentalReservations) }}</p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Ingresos por rentas</p>
                            <p class="{{ $kpiVal }} text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($totalRentalRevenue, 2, '.', ',') }}
                            </p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Comisiones por ventas</p>
                            <p class="{{ $kpiVal }} text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($salesCommissionTotal, 2, '.', ',') }}
                            </p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Comisiones por rentas</p>
                            <p class="{{ $kpiVal }} text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($rentalCommissionTotal, 2, '.', ',') }}
                            </p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Cargo cliente (ventas)</p>
                            <p class="{{ $kpiVal }} text-amber-600 dark:text-amber-400">
                                ${{ number_format($salesCustomerChargeTotal, 2, '.', ',') }}
                            </p>
                        </div>
                        <div class="{{ $kpiCard }}">
                            <p class="{{ $kpiTitle }}">Cargo cliente (rentas)</p>
                            <p class="{{ $kpiVal }} text-amber-600 dark:text-amber-400">
                                ${{ number_format($rentalCustomerChargeTotal, 2, '.', ',') }}
                            </p>
                        </div>
                    </div>

                    {{-- ===== Gráficas ===== --}}
                    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Ventas por agente (MXN)</h4>
                            <div class="h-64">
                                <canvas id="salesByAgentChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Rentas por agente (MXN)</h4>
                            <div class="h-64">
                                <canvas id="rentalsByAgentChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- ===== Tablas (ventas/rentas) + Comparativa ===== --}}
                    {{-- ... (idénticas a la versión anterior que ya te di) ... --}}
                    {{-- Para no repetir, conserva los bloques de tablas y comparativa que pegaste antes --}}
                    @include('admin.reports.partials.tables') {{-- opcional si prefieres extraerlas --}}

                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const salesLabels  = @json($salesByAgent->pluck('name'));
        const salesTotals  = @json($salesByAgent->map(fn($a) => round((float)($a->total_sales_amount ?? 0), 2)));
        const rentalLabels = @json($rentalsByAgent->pluck('agent.name'));
        const rentalTotals = @json($rentalsByAgent->map(fn($r) => round((float)$r->total_revenue, 2)));
        const cur = (v)=>'$'+Number(v).toLocaleString();

        new Chart(document.getElementById('salesByAgentChart'), {
            type:'bar',
            data:{ labels:salesLabels, datasets:[{ label:'Ventas (MXN)', data:salesTotals, borderWidth:1 }] },
            options:{ maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ callback:cur } } }, plugins:{ legend:{ display:false } } }
        });

        new Chart(document.getElementById('rentalsByAgentChart'), {
            type:'bar',
            data:{ labels:rentalLabels, datasets:[{ label:'Rentas (MXN)', data:rentalTotals, borderWidth:1 }] },
            options:{ maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, ticks:{ callback:cur } } }, plugins:{ legend:{ display:false } } }
        });
    </script>
</x-app-layout>
