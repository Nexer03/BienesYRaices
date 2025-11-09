<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de solicitud de agente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold">Información del solicitante</h3>
                        <p><span class="font-medium">Nombre:</span> {{ $application->user->name }}</p>
                        <p><span class="font-medium">Email:</span> {{ $application->user->email }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold">Documentación</h3>
                        <p><span class="font-medium">RFC:</span> {{ $application->rfc }}</p>
                        <p><span class="font-medium">CURP:</span> {{ $application->curp }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold">Estado</h3>
                        <p><span class="font-medium">Estado:</span> <span class="capitalize">{{ __($application->status) }}</span></p>
                        @if ($application->rejection_reason)
                            <p><span class="font-medium">Motivo rechazo:</span> {{ $application->rejection_reason }}</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.agent-applications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600">Volver</a>

                        @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                            <form method="POST" action="{{ route('admin.agent-applications.approve', $application) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Aprobar</button>
                            </form>
                            <form method="POST" action="{{ route('admin.agent-applications.reject', $application) }}" class="flex items-center space-x-2">
                                @csrf
                                <input type="text" name="rejection_reason" required placeholder="Motivo" class="border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Rechazar</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
