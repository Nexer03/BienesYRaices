<section class="bg-white shadow-md rounded-xl p-6 border border-gray-200">
    <header class="mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Actualizar contraseña</h2>
        <p class="mt-1 text-sm text-gray-600">
            Asegúrate de usar una contraseña larga, segura y diferente a las anteriores.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Contraseña actual --}}
        <div>
            <x-input-label for="update_password_current_password" value="Contraseña actual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-600" />
        </div>

        {{-- Nueva contraseña --}}
        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-600" />
        </div>

        {{-- Confirmar nueva contraseña --}}
        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar nueva contraseña" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        {{-- Botón Guardar --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white">
                Guardar cambios
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600">
                    Contraseña actualizada correctamente.
                </p>
            @endif
        </div>
    </form>
</section>
