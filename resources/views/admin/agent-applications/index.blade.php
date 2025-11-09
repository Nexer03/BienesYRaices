<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Solicitudes de agentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach (['success', 'error', 'info'] as $statusKey)
                @if (session($statusKey))
                    <div class="mb-4 px-4 py-3 rounded relative {{ $statusKey === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : ($statusKey === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 'bg-blue-100 border border-blue-400 text-blue-700') }}" role="alert">
                        {{ session($statusKey) }}
                    </div>
                @endif
            @endforeach

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('admin.agent-applications.index') }}" class="mb-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por estado</label>
                        <div class="mt-1 flex space-x-2">
                            <select id="status" name="status" class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm">
                                <option value="" @selected($status === '')>Todos</option>
                                <option value="{{ \App\Models\AgentApplication::STATUS_PENDING }}" @selected($status === \App\Models\AgentApplication::STATUS_PENDING)>Pendientes</option>
                                <option value="{{ \App\Models\AgentApplication::STATUS_APPROVED }}" @selected($status === \App\Models\AgentApplication::STATUS_APPROVED)>Aprobadas</option>
                                <option value="{{ \App\Models\AgentApplication::STATUS_REJECTED }}" @selected($status === \App\Models\AgentApplication::STATUS_REJECTED)>Rechazadas</option>
                            </select>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Filtrar</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFC</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CURP</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo rechazo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($applications as $application)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $application->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $application->user->email }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $application->rfc }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $application->curp }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 capitalize">{{ __($application->status) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $application->rejection_reason ?? '—' }}</td>
                                        <td class="px-4 py-2 space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.agent-applications.show', $application) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Ver detalles</a>
                                            </div>
                                            @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                                                <form method="POST" action="{{ route('admin.agent-applications.approve', $application) }}">
                                                    @csrf
                                                    <button type="submit" class="w-full inline-flex justify-center px-3 py-1 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Aprobar</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.agent-applications.reject', $application) }}" class="space-y-2">
                                                    @csrf
                                                    <input type="text" name="rejection_reason" required placeholder="Motivo" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                                    <button type="submit" class="w-full inline-flex justify-center px-3 py-1 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Rechazar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No hay solicitudes registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
