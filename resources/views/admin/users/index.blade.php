<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestionar Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-semibold mb-4">Lista de Usuarios Registrados</h3>

                    {{-- FORMULARIO DE FILTRADO --}}
                    {{-- AÑADIDO: id="users-filter-form" --}}
                    <form id="users-filter-form" action="{{ route('admin.users.index') }}" method="GET" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            {{-- Filtro por Nombre --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $filters['name'] ?? '' }}">
                            </div>

                            {{-- Filtro por Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="text" name="email" id="email" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $filters['email'] ?? '' }}">
                            </div>

                            {{-- Filtro por Rol --}}
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                                <select name="role" id="role" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Todos</option>
                                    <option value="admin" @selected(($filters['role'] ?? '') == 'admin')>Admin</option>
                                    <option value="agent" @selected(($filters['role'] ?? '') == 'agent')>Agent</option>
                                    <option value="client" @selected(($filters['role'] ?? '') == 'client')>Client</option>
                                </select>
                            </div>

                            {{-- Botones (Submit no es necesario para AJAX, pero lo dejamos por si JS falla) --}}
                            <div class="flex space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Filtrar
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                    {{-- FIN FORMULARIO DE FILTRADO --}}

                    {{-- TABLA DE USUARIOS (CORREGIDA) --}}
                    <div class="overflow-x-auto">
                        <table id="users-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            {{-- Incluye el cuerpo de la tabla desde el archivo parcial --}}
                            @include('admin.users._table_body', ['users' => $users])
                        </table>
                    </div>
                    {{-- FIN TABLA DE USUARIOS --}}

                    {{-- Paginación --}}

                    {{-- ===================== EDIT USER MODAL HTML ===================== --}}
                    <div id="editUserModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-lg relative">
                            {{-- Close Button --}}
                            <button onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                &times; {{-- Simple close icon --}}
                            </button>

                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Editar Usuario</h3>

                            {{-- Edit Form --}}
                            <form id="editUserForm" method="POST" action=""> {{-- Action will be set by JS --}}
                                @csrf
                                @method('PUT') {{-- Use PUT for update --}}

                                <input type="hidden" id="edit_user_id" name="user_id"> {{-- Although not needed for route, useful for JS --}}

                                {{-- Name --}}
                                <div class="mb-4">
                                    <label for="edit_name" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                    <input id="edit_name" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm" type="text" name="name" required />
                                    <p id="edit_name_error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p> {{-- For validation errors --}}
                                </div>

                                {{-- Email --}}
                                <div class="mb-4">
                                    <label for="edit_email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input id="edit_email" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm" type="email" name="email" required />
                                    <p id="edit_email_error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                                </div>

                                {{-- Role --}}
                                <div class="mb-4">
                                    <label for="edit_role" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                                    <select id="edit_role" name="role" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm" required>
                                        <option value="client">Client</option>
                                        <option value="agent">Agent</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <p id="edit_role_error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                                </div>

                                <div class="flex justify-end gap-4 mt-6">
                                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- =================== END EDIT USER MODAL =================== --}}
                    {{-- ================= DELETE CONFIRMATION MODAL ================= --}}
                    <div id="deleteConfirmModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md relative modal-content">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirmar Eliminación</h3>
                            <p class="mb-6 text-gray-600 dark:text-gray-400">¿Estás seguro de que quieres eliminar al usuario <strong id="deleteUserName"></strong>? Esta acción no se puede deshacer.</p>

                            <form id="deleteConfirmForm" method="POST" action=""> {{-- Action set by JS --}}
                                @csrf
                                @method('DELETE')
                                <div class="flex justify-end gap-4">
                                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancelar</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Eliminar Usuario</button>
                                </div>
                            </form>
                            <button onclick="closeDeleteModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                &times;
                            </button>
                        </div>
                    </div>
                    {{-- =============== END DELETE CONFIRMATION MODAL =============== --}}

                    {{-- ================= NOTIFICATION MODAL (Success/Error) ================= --}}
                    <div id="notificationModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-[60] hidden p-4"> {{-- Higher z-index --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-sm relative modal-content border-l-4"> {{-- Added border --}}
                            <p id="notificationMessage" class="text-gray-900 dark:text-gray-100"></p>
                            <button onclick="closeNotificationModal()" class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">&times;</button>
                        </div>
                    </div>
                    {{-- =============== END NOTIFICATION MODAL =============== --}}
                    {{-- CORREGIDO: Añadido id="pagination-links" --}}
                    <div class="mt-4" id="pagination-links">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script para el filtrado AJAX --}}
   @push('scripts')
<script>
    // <<< Declare Modal Element Variables Globally >>>
    let editModal, editForm, editNameInput, editEmailInput, editRoleSelect, editUserIdInput;
    let deleteConfirmModal, deleteConfirmForm, deleteUserNameSpan;
    let notificationModal, notificationMessage;
    // <<< START: Ensure DOM elements exist before adding listeners >>>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Admin Users Script Initializing..."); // Debug log

        // Filter elements
        const filterForm = document.querySelector('#users-filter-form');
        const nameInput = document.querySelector('#name');
        const emailInput = document.querySelector('#email');
        const roleSelect = document.querySelector('#role');
        const tableBody = document.querySelector('#users-table tbody');
        const paginationContainer = document.querySelector('#pagination-links');

        // <<< Assign Modal Elements Inside DOMContentLoaded >>>
        editModal = document.getElementById('editUserModal');
        editForm = document.getElementById('editUserForm');
        editNameInput = document.getElementById('edit_name');
        editEmailInput = document.getElementById('edit_email');
        editRoleSelect = document.getElementById('edit_role');
        editUserIdInput = document.getElementById('edit_user_id');
        deleteConfirmModal = document.getElementById('deleteConfirmModal');
        deleteConfirmForm = document.getElementById('deleteConfirmForm');
        deleteUserNameSpan = document.getElementById('deleteUserName');
        notificationModal = document.getElementById('notificationModal');
        notificationMessage = document.getElementById('notificationMessage');
        // <<< End Assignment >>>

        let debounceTimer;

        // --- Function: Fetch Users via AJAX ---
        function fetchUsers(page = 1) {
            console.log("Fetching users for page:", page); // Debug log
            const currentName = nameInput ? nameInput.value : '';
            const currentEmail = emailInput ? emailInput.value : '';
            const currentRole = roleSelect ? roleSelect.value : '';
            const params = new URLSearchParams({ name: currentName, email: currentEmail, role: currentRole, page: page }).toString();

            if (tableBody) tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4">Cargando...</td></tr>';

            fetch(`{{ route('admin.users.index') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(response => {
                if (!response.ok) { throw new Error(`Network response error: ${response.status}`); }
                return response.text();
            })
            .then(html => {
                if (tableBody) tableBody.innerHTML = html;
                // Add logic here if you want to update pagination links via AJAX too
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-red-500">Error al cargar datos.</td></tr>';
            });
        }

        // --- Function: Debounce for Live Search (If you want it back) ---
         function debounceFetch() {
             clearTimeout(debounceTimer);
             debounceTimer = setTimeout(() => fetchUsers(1), 300);
        }

        // --- Event Listeners: Live Filtering ---
        if (nameInput) { nameInput.addEventListener('input', () => fetchUsers(1)); } // No debounce for instant
        if (emailInput) { emailInput.addEventListener('input', () => fetchUsers(1)); } // No debounce for instant
        if (roleSelect) { roleSelect.addEventListener('change', () => fetchUsers(1)); }
        if (filterForm) {
            filterForm.addEventListener('submit', function(event) {
                event.preventDefault();
                fetchUsers(1);
            });
        }

        // --- Event Listener: Pagination ---
        if (paginationContainer) {
            paginationContainer.addEventListener('click', function (event) {
                const link = event.target.closest('a.page-link');
                if (link && link.href) {
                    event.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    if (page) { fetchUsers(page); }
                }
            });
        }

        // --- Event Listener: Edit Form Submission ---
        if (editForm) {
            editForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(editForm);
                const actionUrl = editForm.action;

                // Clear previous errors before submitting
                 document.querySelectorAll('[id$=_error]').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });

                fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json()) // Always expect JSON back
                .then(data => {
                    if (data.success) {
                        closeEditModal();
                        showSuccessModal(data.message || 'Usuario actualizado.');
                        fetchUsers(); // Refresh the table content via AJAX
                    } else if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`edit_${key}_error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    } else {
                        showErrorModal(data.message || 'Ocurrió un error.');
                    }
                })
                .catch(error => {
                    console.error('Error submitting edit form:', error);
                    showErrorModal('Ocurrió un error de red.');
                });
            });
        } // End if(editForm)

    }); // <<< END: DOMContentLoaded Listener >>>


    // --- GLOBAL FUNCTIONS (accessible by onclick) ---

    // --- Function: Open Edit Modal ---
    function openEditModal(button) {
        console.log("Opening Edit Modal"); // Debug log
        // Clear previous errors
         document.querySelectorAll('[id$=_error]').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });

        // Populate form (Now uses global variables)
        if(editUserIdInput) editUserIdInput.value = button.dataset.userId;
        if(editNameInput) editNameInput.value = button.dataset.userName;
        if(editEmailInput) editEmailInput.value = button.dataset.userEmail;
        if(editRoleSelect) editRoleSelect.value = button.dataset.userRole;
        if(editForm) editForm.action = button.dataset.updateUrl;

        // Show modal
        if (editModal) editModal.classList.remove('hidden');
        else console.error("Edit modal element not found globally."); // Debug if modal is null
    }

    // --- Function: Close Edit Modal ---
    function closeEditModal() {
         console.log("Closing Edit Modal"); // Debug log
        if (editModal) editModal.classList.add('hidden');
    }

    // --- Function: Open Delete Modal ---
    function openDeleteModal(button) {
        console.log("Opening Delete Modal"); // Debug log
        const userName = button.dataset.userName;
        const deleteUrl = button.dataset.deleteUrl;

        // Use global variables
        if (deleteUserNameSpan) deleteUserNameSpan.textContent = userName;
        if (deleteConfirmForm) deleteConfirmForm.action = deleteUrl;
        if (deleteConfirmModal) deleteConfirmModal.classList.remove('hidden');
         else console.error("Delete modal element not found globally."); // Debug if modal is null
    }

    // --- Function: Close Delete Modal ---
    function closeDeleteModal() {
        console.log("Closing Delete Modal"); // Debug log
        if (deleteConfirmModal) deleteConfirmModal.classList.add('hidden');
    }

    // --- Functions: Notification Modals ---
    function showSuccessModal(message) {
        console.log("Showing Success Modal:", message); // Debug log
        if (notificationMessage) notificationMessage.textContent = message;
        if (notificationModal) {
            const content = notificationModal.querySelector('.modal-content');
            if(content) {
                content.classList.remove('border-red-500');
                content.classList.add('border-green-500');
            }
            notificationModal.classList.remove('hidden');
        }
        setTimeout(closeNotificationModal, 3000);
    }

    function showErrorModal(message) {
        // Uses global variables
        console.log("Showing Error Modal:", message);
        if (notificationMessage) notificationMessage.textContent = message;
        if (notificationModal) {
             const content = notificationModal.querySelector('.modal-content');
             if(content) { content.classList.remove('border-green-500'); content.classList.add('border-red-500'); }
            notificationModal.classList.remove('hidden');
        }
        setTimeout(closeNotificationModal, 5000);
    }

    function closeNotificationModal() {
        if (notificationModal) notificationModal.classList.add('hidden');
    }

    // --- Global Event Listener: Close modals on outside click ---
     window.addEventListener('click', function(event) {
        if (editModal && event.target === editModal) closeEditModal();
        if (deleteConfirmModal && event.target === deleteConfirmModal) closeDeleteModal();
        if (notificationModal && event.target === notificationModal) closeNotificationModal();
     });

</script>
@endpush
</x-app-layout>
