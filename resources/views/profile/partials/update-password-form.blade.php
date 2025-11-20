<section class="bg-white dark:bg-slate-900 shadow-md rounded-xl p-6 border border-gray-200 dark:border-slate-800">
    <header class="mb-4">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-slate-100">
            Actualizar contraseña
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
            Asegúrate de usar una contraseña larga, segura y diferente a las anteriores.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Contraseña actual --}}
        <div>
            <x-input-label for="update_password_current_password"
                value="Contraseña actual"
                class="dark:text-slate-100" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-600" />
        </div>

        {{-- Nueva contraseña --}}
        <div>
            <x-input-label for="update_password_password"
                value="Nueva contraseña"
                class="dark:text-slate-100" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-600" />
        </div>

        {{-- Confirmar nueva contraseña --}}
        <div>
            <x-input-label for="update_password_password_confirmation"
                value="Confirmar nueva contraseña"
                class="dark:text-slate-100" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        {{-- Botón Guardar --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-700 dark:hover:bg-blue-800">
                Guardar cambios
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 dark:text-green-400">
                    Contraseña actualizada correctamente.
                </p>
            @endif
        </div>
    </form>
</section>
