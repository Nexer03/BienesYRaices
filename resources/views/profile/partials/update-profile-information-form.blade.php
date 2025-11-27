<section class="bg-white dark:bg-slate-900 shadow-md rounded-xl p-6 border border-gray-200 dark:border-slate-800">
    <header class="flex items-center gap-4 mb-6">
        {{-- Imagen de perfil --}}
        <div class="relative">
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Foto de perfil actual"
                    class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                    alt="Avatar por defecto"
                    class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
            @endif
        </div>

        {{-- Título e instrucciones --}}
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-slate-100">Mi Perfil</h2>
            <p class="text-gray-600 dark:text-slate-300 text-sm">
                Actualiza tu información personal y la foto de tu cuenta.
            </p>
        </div>
    </header>

    {{-- Formulario --}}
    <form method="post" action="{{ route('profile.update') }}" class="space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Nombre --}}
        <div>
            <x-input-label for="name" value="Nombre completo" class="dark:text-slate-100" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-red-600" :messages="$errors->get('name')" />
        </div>

        {{-- Correo electrónico --}}
        <div>
            <x-input-label for="email" value="Correo electrónico" class="dark:text-slate-100" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2 text-red-600" :messages="$errors->get('email')" />
        </div>

        {{-- Teléfono --}}
        <div>
            <x-input-label for="phone" value="Teléfono" class="dark:text-slate-100" />
            <x-text-input id="phone" name="phone" type="text"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                value="{{ old('phone', $user->phone) }}" autocomplete="tel" />
            <x-input-error class="mt-2 text-red-600" :messages="$errors->get('phone')" />
        </div>

        {{-- Biografía --}}
        <div>
            <x-input-label for="bio" value="Biografía" class="dark:text-slate-100" />
            <textarea id="bio" name="bio"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-gray-800 resize-none
                       dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100 dark:placeholder-slate-400"
                rows="3">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2 text-red-600" :messages="$errors->get('bio')" />
        </div>

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" value="Cambiar foto de perfil" class="dark:text-slate-100" />
            <div class="mt-2 flex items-center gap-4">
                <label for="avatar"
                    class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow transition text-sm font-medium">
                    Seleccionar imagen
                </label>
                <x-text-input id="avatar" name="avatar" type="file" accept="image/*" class="hidden"
                    onchange="previewAvatar(event)" />
                <span id="avatarFileName" class="text-gray-500 dark:text-slate-300 text-sm"></span>
            </div>
            <x-input-error class="mt-2 text-red-600" :messages="$errors->get('avatar')" />
        </div>

        {{-- Botón guardar --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-slate-700">
            <x-primary-button class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-700 dark:hover:bg-blue-800">
                {{ __('Guardar cambios') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Cambios guardados correctamente.') }}
                </p>
            @endif
        </div>
    </form>
</section>

{{-- Script de vista previa de avatar --}}
<script>
function previewAvatar(event) {
    const input = event.target;
    const fileName = input.files[0]?.name || "";
    document.getElementById('avatarFileName').textContent = fileName;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Busca la primera imagen dentro de esta sección de perfil
            const img = document.querySelector('section img');
            if (img) {
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
