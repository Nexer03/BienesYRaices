<tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
    @forelse ($users as $user)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $user->role }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                {{-- Botón Editar (Llama a JS para abrir modal) --}}
                <button type="button"
                        onclick="openEditModal(this)"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}"
                        data-user-email="{{ $user->email }}"
                        data-user-role="{{ $user->role }}"
                        data-update-url="{{ route('admin.users.update', $user) }}"
                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                    Editar
                </button>

                {{-- Botón Eliminar (Llama a JS para abrir modal de confirmación) --}}
                <button type="button"
                        onclick="openDeleteModal(this)"
                        data-user-name="{{ $user->name }}"
                        data-delete-url="{{ route('admin.users.destroy', $user) }}"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 ml-2"> {{-- Añadido ml-2 para espaciado --}}
                    Eliminar
                </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No hay usuarios que coincidan con los filtros.</td>
        </tr>
    @endforelse
</tbody>
