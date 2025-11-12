<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reporte de Visitas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-7xl mx-auto px-4 py-12 space-y-10">

    <h2 class="text-3xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
      <i class="fa-solid fa-person-walking text-blue-600"></i> Reporte de Visitas
    </h2>

    {{-- ========== RESUMEN GENERAL ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Resumen general</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600">Visitas registradas</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalVisits }}</p>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600">Estados diferentes</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900">{{ $visitsByStatus->count() }}</p>
        </div>

        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
          <h4 class="text-sm font-semibold text-gray-600">Próximas visitas agendadas</h4>
          <p class="mt-2 text-3xl font-bold text-gray-900">{{ $upcomingVisitsCount }}</p>
        </div>
      </div>
    </section>

    {{-- ========== VISITAS POR ESTADO / AGENTE ========== --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      {{-- Por estado --}}
      <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Visitas por estado</h3>
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse ($visitsByStatus as $status)
                <tr>
                  <td class="px-6 py-4 text-sm font-medium text-gray-800">
                    {{ ucfirst(__($status->status ?? 'Sin estado')) }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $status->total }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                    No hay visitas registradas para mostrar.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Por agente --}}
      <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Visitas por agente</h3>
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Agente</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Visitas atendidas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse ($visitsByAgent as $agent)
                <tr>
                  <td class="px-6 py-4 text-sm font-medium text-gray-800">
                    {{ $agent->agent?->name ?? 'Sin asignar' }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $agent->total }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                    No hay visitas asociadas a agentes para mostrar.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    {{-- ========== EVOLUCIÓN MENSUAL ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Evolución mensual</h3>
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Mes</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total de visitas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse ($visitsByMonth as $month)
              <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                  {{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->translatedFormat('F Y') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $month->total }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                  Aún no hay visitas registradas en el sistema.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- ========== PRÓXIMAS VISITAS ========== --}}
    <section>
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Próximas visitas</h3>
      <div class="overflow-x-auto bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Propiedad</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Agente</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse ($upcomingVisits as $visit)
              <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                  {{ $visit->visit_date?->format('d M Y H:i') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $visit->property?->title ?? 'Sin propiedad' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $visit->agent?->name ?? 'Sin agente' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $visit->client?->name ?? 'Sin cliente' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                  {{ $visit->status ?? 'pendiente' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                  No hay visitas próximas agendadas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

</body>
</html>
