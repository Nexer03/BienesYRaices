<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestión de Usuarios</title>

  {{-- Anti-flash: aplica tema guardado ANTES de pintar la página --}}
  <script>
    (function () {
        try {
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {
            document.documentElement.classList.remove('dark');
        }
    })();
  </script>

  {{-- Tailwind --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
        darkMode: 'class'
    };
  </script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    /* Fade suave para todo al cambiar de tema */
    html.theme-fade * {
        transition:
            background-color .35s ease,
            color .35s ease,
            border-color .35s ease,
            fill .35s ease;
    }

    /* Botón de tema: animación */
    #theme-toggle {
        transition: background-color .25s ease,
                    color .25s ease,
                    transform .25s ease,
                    box-shadow .25s ease;
    }

    #theme-toggle.theme-bounce {
        transform: translateY(-1px) scale(1.03);
        box-shadow: 0 15px 30px rgba(0,0,0,.18);
    }

    #theme-toggle-icon {
        transition: transform .35s ease, opacity .2s ease;
    }

    #theme-toggle-icon.theme-spin {
        transform: rotate(180deg);
    }

    .dark footer {
        background-color: #020617 !important; /* slate-950 */
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-slate-950 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-300">
  {{-- HEADER GLOBAL --}}
  <x-main-header />

  {{-- Botón Tema (igual que en otras vistas) --}}
  <button id="theme-toggle"
          class="fixed bottom-6 right-6 z-50 inline-flex items-center gap-2 px-4 py-2 rounded-full shadow-lg
                 bg-white text-gray-800 hover:bg-gray-100
                 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
          aria-label="Cambiar tema">
      <i id="theme-toggle-icon" class="fa-solid"></i>
      <span class="text-sm font-medium"></span>
  </button>

  <main class="max-w-7xl mx-auto px-4 py-12 flex-1">
    <h2 class="text-3xl font-semibold text-gray-900 dark:text-gray-100 mb-8 flex items-center gap-2">
      <i class="fa-solid fa-users text-blue-600"></i> Gestión de Usuarios
    </h2>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
      <div class="mb-6 bg-green-100 dark:bg-emerald-900/40 border border-green-300 dark:border-emerald-700 text-green-800 dark:text-emerald-200 px-4 py-3 rounded-lg shadow-sm">
        {{ session('success') }}
      </div>
    @endif

    {{-- FILTROS --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-6 mb-10 border border-gray-200 dark:border-slate-800">
      <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100 flex items-center gap-2">
        <i class="fa-solid fa-filter text-blue-600"></i> Filtrar Usuarios
      </h3>

      <form id="users-filter-form" action="{{ route('admin.users.index') }}" method="GET" class="grid md:grid-cols-4 gap-4">
        <div>
          <label for="name" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Nombre</label>
          <input type="text" name="name" id="name"
                 value="{{ $filters['name'] ?? '' }}"
                 class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label for="email" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Email</label>
          <input type="text" name="email" id="email"
                 value="{{ $filters['email'] ?? '' }}"
                 class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label for="role" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Rol</label>
          <select name="role" id="role"
                  class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Todos</option>
            <option value="admin" @selected(($filters['role'] ?? '') == 'admin')>Admin</option>
            <option value="agent" @selected(($filters['role'] ?? '') == 'agent')>Agente</option>
            <option value="client" @selected(($filters['role'] ?? '') == 'client')>Cliente</option>
          </select>
        </div>

        <div class="flex gap-3 items-end">
          <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
          </button>
          <a href="{{ route('admin.users.index') }}"
             class="bg-gray-200 dark:bg-slate-800 dark:text-slate-100 text-gray-700 px-5 py-2 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-slate-700 transition">
            Limpiar
          </a>
        </div>
      </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow overflow-hidden border border-gray-200 dark:border-slate-800">
      <table id="users-table" class="min-w-full divide-y divide-gray-200 dark:divide-slate-800">
        <thead class="bg-gray-100 dark:bg-slate-800">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">ID</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Nombre</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Email</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Rol</th>
            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">Acciones</th>
          </tr>
        </thead>
        @include('admin.users._table_body', ['users' => $users])
      </table>
    </div>

    <div id="pagination-links" class="mt-6">
      {{ $users->links() }}
    </div>
  </main>

  {{-- MODAL EDITAR --}}
  <div id="editUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-8 w-full max-w-lg shadow-xl relative border border-gray-200 dark:border-slate-800">
      <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Editar Usuario</h3>

      <form id="editUserForm" method="POST" action="">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_user_id" name="user_id">

        <div class="space-y-4">
          <div>
            <label for="edit_name" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Nombre</label>
            <input id="edit_name" name="name" required class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
            <p id="edit_name_error" class="text-sm text-red-600 hidden"></p>
          </div>

          <div>
            <label for="edit_email" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Email</label>
            <input id="edit_email" name="email" type="email" required class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
            <p id="edit_email_error" class="text-sm text-red-600 hidden"></p>
          </div>

          <div>
            <label for="edit_role" class="block font-medium mb-1 text-gray-800 dark:text-gray-100">Rol</label>
            <select id="edit_role" name="role" required class="w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500">
              <option value="client">Cliente</option>
              <option value="agent">Agente</option>
              <option value="admin">Administrador</option>
            </select>
            <p id="edit_role_error" class="text-sm text-red-600 hidden"></p>
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-800 dark:text-slate-100 text-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-700">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL ELIMINAR --}}
  <div id="deleteConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-8 w-full max-w-md shadow-xl relative border border-gray-200 dark:border-slate-800">
      <button onclick="closeDeleteModal()" class="absolute top-3 right-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <h3 class="text-xl font-semibold mb-4 text-red-600 flex items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación
      </h3>
      <p class="text-gray-700 dark:text-gray-300 mb-6">
        ¿Estás seguro de eliminar al usuario <strong id="deleteUserName"></strong>? Esta acción no se puede deshacer.
      </p>

      <form id="deleteConfirmForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="flex justify-end gap-3">
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-800 dark:text-slate-100 text-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-slate-700">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Eliminar Usuario
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- MODAL NOTIFICACIONES --}}
  <div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[60]">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl px-6 py-4 border-l-4 border-blue-500 dark:border-blue-400 w-full max-w-sm">
      <p id="notificationMessage" class="text-gray-800 dark:text-gray-100 font-medium"></p>
    </div>
  </div>

  {{-- FOOTER --}}
  <x-main-footer />

  <script>
    /* --- TU SCRIPT ORIGINAL INTACTO --- */
    let editModal, editForm, editNameInput, editEmailInput, editRoleSelect, editUserIdInput;
    let deleteConfirmModal, deleteConfirmForm, deleteUserNameSpan;
    let notificationModal, notificationMessage;

    document.addEventListener('DOMContentLoaded', function () {
      const filterForm = document.querySelector('#users-filter-form');
      const nameInput = document.querySelector('#name');
      const emailInput = document.querySelector('#email');
      const roleSelect = document.querySelector('#role');
      const tableBody = document.querySelector('#users-table tbody');
      const paginationContainer = document.querySelector('#pagination-links');

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

      function fetchUsers(page = 1) {
        const params = new URLSearchParams({
          name: nameInput?.value || '',
          email: emailInput?.value || '',
          role: roleSelect?.value || '',
          page
        }).toString();

        if (tableBody) tableBody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-gray-500">Cargando...</td></tr>';

        fetch(`{{ route('admin.users.index') }}?${params}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
        })
        .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
        .then(html => { if (tableBody) tableBody.innerHTML = html; })
        .catch(() => { if (tableBody) tableBody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-red-500">Error al cargar datos.</td></tr>'; });
      }

      if (nameInput) nameInput.addEventListener('input', () => fetchUsers(1));
      if (emailInput) emailInput.addEventListener('input', () => fetchUsers(1));
      if (roleSelect) roleSelect.addEventListener('change', () => fetchUsers(1));
      if (filterForm) filterForm.addEventListener('submit', e => { e.preventDefault(); fetchUsers(1); });

      if (paginationContainer) {
        paginationContainer.addEventListener('click', e => {
          const link = e.target.closest('a.page-link');
          if (link && link.href) {
            e.preventDefault();
            const page = new URL(link.href).searchParams.get('page');
            if (page) fetchUsers(page);
          }
        });
      }

      if (editForm) {
        editForm.addEventListener('submit', function(event) {
          event.preventDefault();
          const formData = new FormData(editForm);
          const actionUrl = editForm.action;
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
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              closeEditModal();
              showSuccessModal(data.message || 'Usuario actualizado.');
              fetchUsers();
            } else if (data.errors) {
              Object.keys(data.errors).forEach(k => {
                const el = document.getElementById(`edit_${k}_error`);
                if (el) { el.textContent = data.errors[k][0]; el.classList.remove('hidden'); }
              });
            } else showErrorModal(data.message || 'Error.');
          })
          .catch(() => showErrorModal('Error de red.'));
        });
      }
    });

    function openEditModal(button) {
      document.querySelectorAll('[id$=_error]').forEach(el => { el.textContent = ''; el.classList.add('hidden'); });
      editUserIdInput.value = button.dataset.userId;
      editNameInput.value = button.dataset.userName;
      editEmailInput.value = button.dataset.userEmail;
      editRoleSelect.value = button.dataset.userRole;
      editForm.action = button.dataset.updateUrl;
      editModal.classList.remove('hidden');
    }

    function closeEditModal() { editModal.classList.add('hidden'); }
    function openDeleteModal(button) {
      deleteUserNameSpan.textContent = button.dataset.userName;
      deleteConfirmForm.action = button.dataset.deleteUrl;
      deleteConfirmModal.classList.remove('hidden');
    }
    function closeDeleteModal() { deleteConfirmModal.classList.add('hidden'); }
    function showSuccessModal(msg) {
      notificationMessage.textContent = msg;
      notificationModal.classList.remove('hidden');
      setTimeout(closeNotificationModal, 3000);
    }
    function showErrorModal(msg) {
      notificationMessage.textContent = msg;
      notificationModal.classList.remove('hidden');
      setTimeout(closeNotificationModal, 5000);
    }
    function closeNotificationModal() { notificationModal.classList.add('hidden'); }
    window.addEventListener('click', e => {
      if (e.target === editModal) closeEditModal();
      if (e.target === deleteConfirmModal) closeDeleteModal();
      if (e.target === notificationModal) closeNotificationModal();
    });
  </script>

  {{-- Lógica del botón de tema --}}
  <script>
    (function () {
        const html  = document.documentElement;
        const btn   = document.getElementById('theme-toggle');
        const icon  = document.getElementById('theme-toggle-icon');
        const label = btn?.querySelector('span');

        function setIconAndLabel() {
            const isDark = html.classList.contains('dark');
            if (!icon || !label) return;

            icon.classList.remove('fa-sun', 'fa-moon');
            icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
            label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
        }

        function startPageFade() {
            html.classList.add('theme-fade');
            setTimeout(() => html.classList.remove('theme-fade'), 400);
        }

        function animateButton() {
            if (!btn || !icon) return;
            btn.classList.add('theme-bounce');
            icon.classList.add('theme-spin');
            setTimeout(() => {
                btn.classList.remove('theme-bounce');
                icon.classList.remove('theme-spin');
            }, 350);
        }

        function apply(mode) {
            const isDark = mode === 'dark';
            startPageFade();
            html.classList.toggle('dark', isDark);
            try {
                localStorage.setItem('theme', mode);
            } catch (e) {}
            setIconAndLabel();
            animateButton();
        }

        // Estado inicial de icono/texto según clase actual del <html>
        setIconAndLabel();

        btn?.addEventListener('click', () => {
            const next = html.classList.contains('dark') ? 'light' : 'dark';
            apply(next);
        });
    })();
  </script>
</body>
</html>
