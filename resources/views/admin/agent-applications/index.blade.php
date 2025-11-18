<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Solicitudes de agentes — Sin beca no hay renta</title>

  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

    <!-- Iconos -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<body class="min-h-screen bg-gray-50 text-gray-800 flex flex-col">
  <!-- HEADER GLOBAL -->
  <x-main-header />

  <!-- CONTENIDO -->
  <main class="flex-1 max-w-7xl mx-auto px-6 py-10">
    <header class="mb-10 text-center">
      <h1 class="text-4xl font-extrabold text-gray-800">Gestión de solicitudes de agentes</h1>
      <p class="text-gray-500 mt-2">Aprueba, rechaza o revisa solicitudes pendientes con facilidad.</p>
    </header>

    {{-- Alertas --}}
    @foreach (['success', 'error', 'info'] as $statusKey)
      @if (session($statusKey))
        <div class="mb-6 px-4 py-3 rounded-lg shadow text-sm
          {{ $statusKey === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
            ($statusKey === 'error' ? 'bg-red-50 text-red-700 border border-red-200' :
            'bg-blue-50 text-blue-700 border border-blue-200') }}">
          {{ session($statusKey) }}
        </div>
      @endif
    @endforeach

    {{-- Filtro --}}
    <section class="bg-white shadow-md rounded-2xl p-6 mb-8 border border-gray-100">
      <form method="GET" action="{{ route('admin.agent-applications.index') }}" class="flex flex-col md:flex-row gap-4 md:items-end">
        <div class="flex-1">
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por estado</label>
          <select id="status" name="status"
            class="w-full border border-gray-300 rounded-lg text-gray-700 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="" @selected($status === '')>Todos</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_PENDING }}" @selected($status === \App\Models\AgentApplication::STATUS_PENDING)>Pendientes</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_APPROVED }}" @selected($status === \App\Models\AgentApplication::STATUS_APPROVED)>Aprobadas</option>
            <option value="{{ \App\Models\AgentApplication::STATUS_REJECTED }}" @selected($status === \App\Models\AgentApplication::STATUS_REJECTED)>Rechazadas</option>
          </select>
        </div>
        <button type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
          Filtrar
        </button>
      </form>
    </section>

    {{-- Tabla --}}
    <section class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
          <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-6 py-3 font-semibold">Solicitante</th>
              <th class="px-6 py-3 font-semibold">RFC</th>
              <th class="px-6 py-3 font-semibold">CURP</th>
              <th class="px-6 py-3 font-semibold">Estado</th>
              <th class="px-6 py-3 font-semibold">Motivo rechazo</th>
              <th class="px-6 py-3 font-semibold text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            @forelse ($applications as $application)
              <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                  <div class="font-semibold text-gray-800">{{ $application->user->name }}</div>
                  <div class="text-gray-500 text-xs">{{ $application->user->email }}</div>
                </td>
                <td class="px-6 py-4">{{ $application->rfc }}</td>
                <td class="px-6 py-4">{{ $application->curp }}</td>
                <td class="px-6 py-4 capitalize">
                  @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold">Pendiente</span>
                  @elseif ($application->status === \App\Models\AgentApplication::STATUS_APPROVED)
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">Aprobada</span>
                  @else
                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">Rechazada</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $application->rejection_reason ?? '—' }}</td>
                <td class="px-6 py-4 text-right space-y-2">
                  <a href="{{ route('admin.agent-applications.show', $application) }}"
                    class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Ver detalles</a>

                  @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                    <form method="POST" action="{{ route('admin.agent-applications.approve', $application) }}">
                      @csrf
                      <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs shadow">
                        Aprobar
                      </button>
                    </form>
                    <form method="POST" action="{{ route('admin.agent-applications.reject', $application) }}" class="space-y-2">
                      @csrf
                      <input type="text" name="rejection_reason" required placeholder="Motivo de rechazo"
                        class="w-full border border-gray-300 rounded-lg text-xs px-2 py-1 focus:ring-1 focus:ring-red-500 focus:border-red-500">
                      <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs shadow">
                        Rechazar
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">No hay solicitudes registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
        {{ $applications->links() }}
      </div>
    </section>
  </main>

  <!-- FOOTER GLOBAL -->
  <x-main-footer />
</body>
</html>
