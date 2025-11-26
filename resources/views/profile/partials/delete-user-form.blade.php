<section class="bg-white dark:bg-slate-900 shadow-md rounded-xl p-6 border border-gray-200 dark:border-slate-800">
    <header>
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-slate-100">
            Eliminar cuenta
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
            Una vez que elimines tu cuenta, todos tus datos y recursos se eliminarán de forma permanente.
            Asegúrate de guardar cualquier información importante antes de continuar.
        </p>
    </header>

         <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-4">
        @csrf
        @method('delete')

          <p class="text-sm text-gray-600 dark:text-slate-300">
            Esta acción no se puede deshacer. Todos tus datos y configuraciones serán eliminados de forma permanente.
            Por favor, introduce tu contraseña para confirmar la eliminación.
        </p>

           {{-- Campo contraseña --}}
        <div class="max-w-md">
            <x-input-label for="password" value="Contraseña" class="dark:text-slate-100" />
            <x-text-input id="password" name="password" type="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                placeholder="Ingresa tu contraseña" />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-600" />
        </div>

               <div class="flex items-center gap-3">
            <x-danger-button class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-700 dark:hover:bg-red-800">
                Eliminar cuenta definitivamente
            </x-danger-button>
        </div>
    </form>
</section>
