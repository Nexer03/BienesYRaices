<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar comisión') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    <form method="POST" action="{{ route('admin.commissions.update', $commission) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Agente (opcional)') }}
                                </label>
                                <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('Comisión general') }}</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" @selected(old('user_id', $commission->user_id) == $agent->id)>{{ $agent->name }}</option>
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
                                    <option value="sale" @selected(old('listing_type', $commission->listing_type) === 'sale')>{{ __('Venta') }}</option>
                                    <option value="rent" @selected(old('listing_type', $commission->listing_type) === 'rent')>{{ __('Renta') }}</option>
                                    <option value="both" @selected(old('listing_type', $commission->listing_type) === 'both')>{{ __('Ambos') }}</option>
                                </select>
                                @error('listing_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Comisión del agente (%)') }}
                                </label>
                                <input type="number" step="0.01" min="0" max="100" name="percentage" id="percentage" value="{{ old('percentage', $commission->percentage) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Cargo al cliente (%)') }}
                                </label>
                                <input type="number" step="0.01" min="0" max="100" name="customer_percentage" id="customer_percentage" value="{{ old('customer_percentage', $commission->customer_percentage) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('customer_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="effective_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Vigencia desde') }}
                                </label>
                                <input type="date" name="effective_from" id="effective_from" value="{{ old('effective_from', optional($commission->effective_from)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('effective_from')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Notas internas') }}
                            </label>
                            <input type="text" name="notes" id="notes" value="{{ old('notes', $commission->notes) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.commissions.index') }}" class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('Actualizar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
