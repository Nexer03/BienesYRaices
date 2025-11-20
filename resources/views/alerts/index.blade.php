<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Alertas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Crear alerta</h3>
                <form action="{{ route('alerts.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input name="name" class="w-full rounded border-gray-300" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frecuencia</label>
                        <select name="frequency" class="w-full rounded border-gray-300">
                            <option value="immediate">Inmediata</option>
                            <option value="daily">Diaria</option>
                            <option value="weekly">Semanal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                        <input name="city" class="w-full rounded border-gray-300" placeholder="Ej. Ciudad de México">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio mín.</label>
                            <input name="price_min" type="number" class="w-full rounded border-gray-300" step="0.01">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio máx.</label>
                            <input name="price_max" type="number" class="w-full rounded border-gray-300" step="0.01">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Habitaciones</label>
                            <input name="bedrooms" type="number" class="w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Baños</label>
                            <input name="bathrooms" type="number" class="w-full rounded border-gray-300">
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-2 flex items-center justify-between">
                        <p class="text-xs text-gray-500">Las alertas se entregarán solo en la campanita de notificaciones.</p>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Mis alertas</h3>
                <div class="space-y-4">
                    @forelse($criteria as $alert)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $alert->name }}</h4>
                                    <p class="text-xs text-gray-500">Frecuencia: {{ $alert->frequency }} | Estado: {{ $alert->is_paused ? 'Pausada' : 'Activa' }}</p>
                                    <p class="text-xs text-gray-500">Consentimiento: {{ optional($alert->consented_at)->toDateTimeString() ?? 'Pendiente' }}</p>
                                    <p class="text-xs text-gray-500">Último envío: {{ optional($alert->last_sent_at)->diffForHumans() ?? 'nunca' }} · Coincidencias nuevas: {{ $nextMatches[$alert->id] ?? 0 }}</p>
                                    @php($bell = $alert->channelPreferences->firstWhere('channel', 'bell'))
                                    <p class="text-xs text-gray-500">Campanita: {{ $bell && $bell->isSubscribed() ? 'Activa' : 'Desactivada' }} · Último disparo: {{ optional(optional($bell)->last_sent_at)->diffForHumans() ?? 'nunca' }}</p>
                                </div>
                                <div class="flex gap-2">
                                    @if($alert->is_paused)
                                        <form method="POST" action="{{ route('alerts.resume', $alert) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="px-3 py-1 bg-green-600 text-white rounded">Reanudar</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('alerts.pause', $alert) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="px-3 py-1 bg-yellow-600 text-white rounded">Pausar</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('alerts.destroy', $alert) }}" onsubmit="return confirm('¿Eliminar alerta?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Entrega</h5>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">Las alertas se entregan únicamente en la campanita dentro de la plataforma.</p>
                                </div>
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Últimos envíos</h5>
                                    <ul class="text-xs text-gray-600 dark:text-gray-300 space-y-1">
                                        @foreach($alert->deliveryLogs as $log)
                                            <li>{{ $log->sent_at->toDateTimeString() }} · {{ $log->channel }} · {{ $log->status }} · {{ $log->properties_count }} props</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <details class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-3">
                                <summary class="text-sm font-semibold text-gray-800 dark:text-gray-200 cursor-pointer">Editar preferencias</summary>
                                <form method="POST" action="{{ route('alerts.update', $alert) }}" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @csrf
                                    @method('PATCH')

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                        <input name="name" class="w-full rounded border-gray-300" value="{{ old('name', $alert->name) }}" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frecuencia</label>
                                        <select name="frequency" class="w-full rounded border-gray-300">
                                            <option value="immediate" @selected(old('frequency', $alert->frequency) === 'immediate')>Inmediata</option>
                                            <option value="daily" @selected(old('frequency', $alert->frequency) === 'daily')>Diaria</option>
                                            <option value="weekly" @selected(old('frequency', $alert->frequency) === 'weekly')>Semanal</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
                                        <input name="city" class="w-full rounded border-gray-300" value="{{ old('city', $alert->filters['city'] ?? '') }}" placeholder="Ej. Ciudad de México">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio mín.</label>
                                            <input name="price_min" type="number" class="w-full rounded border-gray-300" step="0.01" value="{{ old('price_min', $alert->filters['price_min'] ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio máx.</label>
                                            <input name="price_max" type="number" class="w-full rounded border-gray-300" step="0.01" value="{{ old('price_max', $alert->filters['price_max'] ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Habitaciones</label>
                                            <input name="bedrooms" type="number" class="w-full rounded border-gray-300" value="{{ old('bedrooms', $alert->filters['bedrooms'] ?? '') }}">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Baños</label>
                                            <input name="bathrooms" type="number" class="w-full rounded border-gray-300" value="{{ old('bathrooms', $alert->filters['bathrooms'] ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-span-1 md:col-span-2 flex items-center justify-between">
                                        <label class="inline-flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" name="is_paused" value="1" class="rounded border-gray-300" @checked(old('is_paused', $alert->is_paused))>
                                            <span>Pausar esta alerta</span>
                                        </label>
                                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar cambios</button>
                                    </div>
                                </form>
                            </details>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 dark:text-gray-300">Aún no tienes alertas configuradas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
