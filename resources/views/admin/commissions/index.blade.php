<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Comisiones del sistema') }}
            </h2>
            <a href="{{ route('admin.commissions.index') }}" class="text-sm text-indigo-500 hover:text-indigo-400">
                {{ __('Actualizar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    @if (session('success'))
                        <div class="rounded-md bg-green-50 dark:bg-green-900/40 p-4 text-sm text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('Registrar nueva comisión') }}</h3>
                        <form method="POST" action="{{ route('admin.commissions.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <form method="POST" action="{{ route('admin.commissions.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            @csrf
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Agente (opcional)') }}
                                </label>
                                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Comisión general') }}</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" @selected(old('user_id') == $agent->id)>{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="listing_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Tipo de operación') }}
                                </label>
                                <select name="listing_type" id="listing_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="sale" @selected(old('listing_type') === 'sale')>{{ __('Venta') }}</option>
                                    <option value="rent" @selected(old('listing_type') === 'rent')>{{ __('Renta') }}</option>
                                    <option value="both" @selected(old('listing_type') === 'both')>{{ __('Ambos') }}</option>
                                </select>
                                @error('listing_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Comisión del agente (%)') }}
                                    {{ __('Porcentaje (%)') }}
                                </label>
                                <input type="number" step="0.01" min="0" max="100" name="percentage" id="percentage" value="{{ old('percentage') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Cargo al cliente (%)') }}
                                </label>
                                <input type="number" step="0.01" min="0" max="100" name="customer_percentage" id="customer_percentage" value="{{ old('customer_percentage') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('customer_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="effective_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Vigencia desde') }}
                                </label>
                                <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('effective_from')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Notas internas') }}
                                </label>
                                <input type="text" name="notes" id="notes" value="{{ old('notes') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Ej. Comisión preferencial por rendimiento') }}" />
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1 flex items-end">
                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Guardar') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">{{ __('Comisiones registradas') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tipo') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Agente') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Comisión del agente') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cargo al cliente') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Porcentaje') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Vigente desde') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Notas') }}</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($commissions as $commission)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ __(match($commission->listing_type) {
                                                'sale' => 'Venta',
                                                'rent' => 'Renta',
                                                default => 'Ambos',
                                            }) }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                {{ $commission->agent?->name ?? __('General del sistema') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">{{ number_format($commission->percentage, 2) }}%</td>
                                            <td class="px-4 py-3 text-sm">{{ number_format($commission->customer_percentage, 2) }}%</td>
                                            <td class="px-4 py-3 text-sm">
                                                {{ optional($commission->effective_from)->format('d/m/Y') ?? __('Sin especificar') }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $commission->notes ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                <div class="inline-flex gap-2">
                                                    <a href="{{ route('admin.commissions.edit', $commission) }}" class="text-indigo-600 hover:text-indigo-400">{{ __('Editar') }}</a>
                                                    <form method="POST" action="{{ route('admin.commissions.destroy', $commission) }}" onsubmit="return confirm('{{ __('¿Deseas eliminar esta comisión?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-400">{{ __('Eliminar') }}</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
                                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                                {{ __('Aún no hay comisiones configuradas.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
