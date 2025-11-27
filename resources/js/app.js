import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// ===== Sincronizar tema claro/oscuro en todas las vistas que cargan app.js =====
(function syncGlobalThemePreference() {
  const root = document.documentElement;

  const readStored = () => (localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

  const apply = (mode) => {
    const isDark = mode === 'dark';
    root.classList.toggle('dark', isDark);

    // Ajusta el toggle si la vista lo incluye
    const btn = document.getElementById('theme-toggle');
    const icon = document.getElementById('theme-toggle-icon');
    const label = btn?.querySelector('span');

    if (icon) {
      icon.classList.remove('fa-sun', 'fa-moon');
      icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');
    }
    if (label) {
      label.textContent = isDark ? 'Modo oscuro' : 'Modo claro';
    }
  };

  const sync = () => apply(readStored());

  document.addEventListener('DOMContentLoaded', sync);
  window.addEventListener('pageshow', sync);
  window.addEventListener('storage', (event) => {
    if (!event.key || event.key === 'theme') {
      sync();
    }
  });

  // Permite que scripts locales pidan actualizar icono/etiqueta
  document.addEventListener('theme:sync', (event) => apply(event.detail?.mode ?? readStored()));
})();
