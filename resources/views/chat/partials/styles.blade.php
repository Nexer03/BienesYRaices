<style>
  :root {
    --chat-bg: #f6f8fb;
    --chat-surface: rgba(255, 255, 255, 0.9);
    --chat-border: rgba(15, 23, 42, 0.08);
    --chat-primary: #2563eb;
    --chat-primary-soft: #eff6ff;
    --chat-muted: #64748b;
  }

  /* Variables en modo oscuro (cuando <html> tiene la clase .dark) */
  .dark {
    --chat-bg: #020617;                     /* slate-950 */
    --chat-surface: rgba(15, 23, 42, 0.96); /* slate-900 */
    --chat-border: rgba(148, 163, 184, 0.45); /* slate-400 */
    --chat-primary: #60a5fa;                /* blue-400 */
    --chat-primary-soft: rgba(37, 99, 235, 0.16);
    --chat-muted: #9ca3af;                  /* gray-400 */
  }

  /* Botón de tema (dark / light) */
  .chat-theme-toggle {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 200;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    border-radius: 999px;
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
    cursor: pointer;
    transition:
      background-color 0.25s ease,
      color 0.25s ease,
      transform 0.25s ease,
      box-shadow 0.25s ease,
      border-color 0.25s ease;
  }

  .chat-theme-toggle:hover {
    background: #f8fafc;
    color: #0f172a;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
  }

  .chat-theme-toggle.is-pressed {
    transform: translateY(-1px) scale(1.03);
    box-shadow: 0 22px 45px rgba(15, 23, 42, 0.16);
  }

  .chat-theme-toggle__icon {
    transition: transform 0.35s ease, opacity 0.2s ease;
  }

  .chat-theme-toggle__icon.is-rotating {
    transform: rotate(180deg);
  }

  /* Modo oscuro para el botón */
  [data-bs-theme="dark"] .chat-theme-toggle,
  .dark .chat-theme-toggle {
    background: #0f172a;
    color: #e2e8f0;
    border-color: rgba(255, 255, 255, 0.06);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
  }

  [data-bs-theme="dark"] .chat-theme-toggle:hover,
  .dark .chat-theme-toggle:hover {
    background: #111827;
    color: #e2e8f0;
  }

  /* ================== Calendario de reservas (Flatpickr) ================== */
    /* ================== Calendario de reservas (Flatpickr) ================== */
  /* Forzar que el calendario tenga sólo 7 columnas (ocultar columna de semanas) */
  .fp-shell .flatpickr-weekwrapper{
    display:none !important;           /* oculta la columna de números de semana si estuviera activa */
  }

  .fp-shell .flatpickr-calendar.hasWeeks .flatpickr-days{
    margin-left:0 !important;          /* corrige el desplazamiento cuando hasWeeks está presente */
  }

  .fp-hidden-input{
    position:absolute !important;
    width:1px !important;
    height:1px !important;
    padding:0 !important;
    margin:-1px !important;
    border:0 !important;
    clip:rect(0 0 0 0) !important;
    overflow:hidden !important;
    white-space:nowrap !important;
  }

  .fp-shell .flatpickr-calendar{
    position:static !important;
    border:0 !important;
    box-shadow:none !important;
    font-size:14px;
  }
  .fp-shell .flatpickr-innerContainer{
    padding:8px 8px 12px;
  }
  .fp-shell .flatpickr-days{
    padding:0 4px 4px;
  }
  .fp-shell .flatpickr-weekdays{
    margin-bottom:4px;
  }
  .fp-shell .flatpickr-weekday{
    font-weight:600;
    font-size:11px;
    color:#6b7280;
  }
  .fp-shell .flatpickr-day{
    border-radius:9999px;
    height:32px;
    line-height:32px;
    margin:1px;
  }
  .fp-shell .flatpickr-day:hover{
    background:#e5f0ff;
    color:#111827;
  }
  .fp-shell .flatpickr-day.selected,
  .fp-shell .flatpickr-day.startRange,
  .fp-shell .flatpickr-day.endRange,
  .fp-shell .flatpickr-day.selected.inRange{
    background:#2563eb;
    color:#ffffff;
  }
  .fp-shell .flatpickr-day.inRange{
    background:#dbeafe;
    color:#1f2937;
  }
  .fp-shell .flatpickr-day.disabled,
  .fp-shell .flatpickr-day.disabled:hover{
    color:#9ca3af;
    background:transparent;
    cursor:not-allowed;
    text-decoration:line-through;
    opacity:0.6;
  }

  /* Navegación de meses */
  .fp-shell .flatpickr-months .flatpickr-prev-month,
  .fp-shell .flatpickr-months .flatpickr-next-month{
    top:10px;
    color:#4b5563;
    fill:#4b5563;
    cursor:pointer;
  }
  .fp-shell .flatpickr-months .flatpickr-prev-month{ left:3px; }
  .fp-shell .flatpickr-months .flatpickr-next-month{ right:3px; }
  .fp-shell .flatpickr-months .flatpickr-prev-month:hover,
  .fp-shell .flatpickr-months .flatpickr-next-month:hover{
    color:#111827;
    fill:#111827;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month{
    font-size:110%;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month .cur-month{
    font-weight:700;
    color:#111827;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month input.cur-year{
    font-weight:600;
  }

  /* Dark mode del calendario */
  .dark .fp-shell .flatpickr-calendar{
    background:#020617 !important;
    color:#e5e7eb;
  }
  .dark .fp-shell .flatpickr-weekday{
    color:#9ca3af;
  }
  .dark .fp-shell .flatpickr-day{
    color:#e5e7eb;
  }
  .dark .fp-shell .flatpickr-day:hover{
    background:#1d4ed8;
  }
  .dark .fp-shell .flatpickr-day.selected,
  .dark .fp-shell .flatpickr-day.startRange,
  .dark .fp-shell .flatpickr-day.endRange,
  .dark .fp-shell .flatpickr-day.selected.inRange{
    background:#3b82f6;
    color:#f9fafb;
  }
  .dark .fp-shell .flatpickr-day.inRange{
    background:rgba(59,130,246,0.25);
    color:#e5e7eb;
  }
  .dark .fp-shell .flatpickr-day.disabled,
  .dark .fp-shell .flatpickr-day.disabled:hover{
    color:#6b7280;
    text-decoration:line-through;
    opacity:0.6;
  }

  /* ====== LOOK tipo Airbnb (modo claro) ====== */
  .fp-shell .flatpickr-months{
    display:flex;
    background:#fff;
    padding:10px 0 20px; /* More bottom padding */
    margin-bottom: 20px; /* More separation from grid */
    position: relative;
  }
  .fp-shell .flatpickr-months .flatpickr-month{
    flex:1;
    color:#222;
    height:50px;
    line-height:50px;
    text-align:center;
    position:relative;
  }
  .fp-shell .flatpickr-months .flatpickr-prev-month,
  .fp-shell .flatpickr-months .flatpickr-next-month{
    position:absolute;
    top:20px; /* Adjusted top to match new padding */
    width:36px; /* Larger touch target */
    height:36px;
    margin-top:-18px; /* Centered */
    border-radius:50%;
    color:#717171;
    fill:#717171;
    cursor:pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
    z-index: 10;
  }
  .fp-shell .flatpickr-months .flatpickr-prev-month{ left:0; } /* At the very edge */
  .fp-shell .flatpickr-months .flatpickr-next-month{ right:0; } /* At the very edge */
  .fp-shell .flatpickr-months .flatpickr-prev-month:hover,
  .fp-shell .flatpickr-months .flatpickr-next-month:hover{
    color:#222;
    fill:#222;
    background-color: #f3f4f6;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month{
    font-size:110%;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month .cur-month{
    font-weight:700;
    color:#222;
  }
  .fp-shell .flatpickr-months .flatpickr-current-month .cur-year{
    font-weight:400;
    color:#717171;
    background:transparent;
  }

  .fp-shell .flatpickr-weekdays{
    display:flex;
    align-items:center;
    height:28px;
    margin-bottom:5px;
  }
  .fp-shell .flatpickr-weekdays .flatpickr-weekdaycontainer{
    flex:1;
    display:flex;
  }
  .fp-shell span.flatpickr-weekday{
    flex:1;
    text-align:center;
    font-size:11px;
    color:#717171!important;
    font-weight:600;
    text-transform:uppercase;
  }

  .fp-shell .flatpickr-days{
    width:100%;
  }
  .fp-shell .dayContainer{
    padding:1px 0 10px;
    min-width:315px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    transform: translate3d(0,0,0);
    opacity: 1;
  }
  .fp-shell .flatpickr-day{
    color:#222;
    border:1px solid transparent;
    background:none;
    border-radius:50%;
    height:38px;
    line-height:38px;
    max-width:38px;
    flex:0 0 14.2857143%;
    text-align:center;
    cursor:pointer;
  }
  .fp-shell .flatpickr-day:hover,
  .fp-shell .flatpickr-day:focus{
    background:#f7f7f7;
    border-color:#f7f7f7;
    outline:0;
  }
  .fp-shell .flatpickr-day.today{
    border-color:#222;
    color:#222;
  }
  .fp-shell .flatpickr-day.today:hover{
    background:#222;
    border-color:#222;
    color:#fff;
  }

  .fp-shell .flatpickr-day.selected,
  .fp-shell .flatpickr-day.startRange,
  .fp-shell .flatpickr-day.endRange{
    background:#222!important;
    color:#fff!important;
    border-color:#222!important;
  }
  .fp-shell .flatpickr-day.inRange{
    background:#f7f7f7!important;
    border-color:#f7f7f7!important;
    box-shadow:-5px 0 0 #f7f7f7,5px 0 0 #f7f7f7;
  }
  .fp-shell .flatpickr-day.startRange{
    border-radius:50% 0 0 50%;
  }
  .fp-shell .flatpickr-day.endRange{
    border-radius:0 50% 50% 0;
  }
  .fp-shell .flatpickr-day.startRange.endRange{
    border-radius:50%;
  }

  /* ==== Fechas no disponibles ==== */
  .fp-shell .flatpickr-day.flatpickr-disabled,
  .fp-shell .flatpickr-day.flatpickr-disabled:hover,
  .fp-shell .flatpickr-day.disabled,
  .fp-shell .flatpickr-day.disabled:hover{
    background-color:#f3f4f6 !important;
    color:#9ca3af !important;
    border-color:transparent !important;
    cursor:not-allowed !important;
    opacity:1 !important;
    text-decoration:none !important;
  }

  .fp-shell .flatpickr-day.flatpickr-disabled.inRange,
  .fp-shell .flatpickr-day.disabled.inRange{
    background-color:#e5e7eb !important;
    color:#9ca3af !important;
  }

  @media (min-width:640px){
    .fp-shell .flatpickr-days .dayContainer:nth-child(1){
      border-right:1px solid #e5e7eb;
    }
  }

  /* ==== Fix para que los días del mes anterior/siguiente ocupen espacio ==== */
  .fp-shell .flatpickr-day.prevMonthDay,
  .fp-shell .flatpickr-day.nextMonthDay {
    display: block !important;
    visibility: visible !important;
    opacity: 0 !important; /* Ocultos visualmente pero ocupan espacio */
    pointer-events: none;
  }

  /* ==================Aqui termina Flatpickr (base) ================== */

  html,
  body {
    height: 100%;
  }

  .chat-app {
    background: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.08), transparent 45%),
                radial-gradient(circle at 80% 0%, rgba(16, 185, 129, 0.08), transparent 40%),
                var(--chat-bg);
    border-radius: 32px;
    padding: clamp(1rem, 2vw, 2rem);
    margin-bottom: 2rem;
    height: 100vh;
    max-height: 100vh;
    display: flex;
    flex-direction: column;
    color: #0f172a;
  }

  .visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  .chat-app__surface {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
    gap: clamp(1.5rem, 3vw, 2.5rem);
    background: var(--chat-surface);
    border-radius: 28px;
    border: 1px solid var(--chat-border);
    box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
    padding: clamp(1.25rem, 3vw, 2rem);
    flex: 1 1 auto;
    min-height: 0;
  }

  .chat-app__main {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    background: rgba(248, 250, 252, 0.96);
    border-radius: 24px;
    padding: 1.25rem 1.5rem 1.5rem;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
  }

  .chat-app__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--chat-border);
    flex: 0 0 auto;
  }

  .chat-partner {
    display: flex;
    align-items: center;
    gap: 0.85rem;
  }

  .chat-partner__avatar {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: linear-gradient(135deg, #2563eb, #38bdf8);
    color: #fff;
    font-weight: 600;
    display: grid;
    place-items: center;
    font-size: 1.125rem;
  }

  .chat-partner__label {
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.7rem;
    color: var(--chat-muted);
    margin-bottom: 0.25rem;
  }

  .chat-status {
    font-size: 0.85rem;
    color: var(--chat-muted);
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .chat-notify-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 1.1rem;
    line-height: 1;
    transition: transform 0.2s ease;
  }

  .chat-notify-btn:hover {
    transform: scale(1.05);
  }

  .status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #cbd5f5;
    display: inline-block;
    box-shadow: 0 0 0 6px rgba(148, 163, 184, 0.15);
    transition: background 0.2s ease, box-shadow 0.2s ease;
  }

  .status-dot.is-online {
    background: #22c55e;
    box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.15);
  }

  .chat-toolbar {
    padding: 1rem 0;
    border-bottom: 1px solid var(--chat-border);
    flex: 0 0 auto;
  }

  .chat-search {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 999px;
    padding: 0.35rem 0.5rem 0.35rem 1rem;
    border: 1px solid var(--chat-border);
    position: relative;
  }

  .chat-search input {
    border: none;
    background: transparent;
    flex: 1;
    outline: none;
  }

  .chat-search__submit {
    border: none;
    background: var(--chat-primary);
    color: #fff;
    border-radius: 999px;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
    cursor: pointer;
  }

  .chat-search__clear {
    border: none;
    background: transparent;
    color: var(--chat-muted);
    font-size: 1rem;
    cursor: pointer;
  }

  .chat-search.is-loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid rgba(37, 99, 235, 0.3);
    border-top-color: var(--chat-primary);
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    position: absolute;
    right: 12px;
  }

  .chat-search-results {
    margin-top: 0.75rem;
    border: 1px solid var(--chat-border);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.85);
    max-height: 220px;
    overflow: hidden;
  }

  .chat-search-results__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 1rem;
    border-bottom: 1px solid var(--chat-border);
    font-size: 0.85rem;
  }

  .chat-search-results__list {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 170px;
    overflow-y: auto;
  }

  .chat-search-results__item,
  .chat-search-results__empty {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    cursor: pointer;
  }

  .chat-search-results__item:last-child,
  .chat-search-results__empty:last-child {
    border-bottom: none;
  }

  .chat-search-results__item:hover {
    background: rgba(37, 99, 235, 0.05);
  }

  .chat-search-results__snippet {
    font-weight: 500;
    margin-bottom: 0.2rem;
  }

  .chat-search-results__time {
    font-size: 0.75rem;
    color: var(--chat-muted);
  }

  .chat-thread {
    flex: 1 1 auto;
    min-height: 0;
    padding: 1.25rem 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    background-color: transparent; /* antes #f9fafb */
  }

  .chat-thread__empty {
    margin: auto;
    text-align: center;
    padding: 2rem;
    border: 1px dashed var(--chat-border);
    border-radius: 20px;
    background: rgba(148, 163, 184, 0.08);
  }

  .message-bubble {
    max-width: 80%;
    padding: 0.85rem 1rem;
    border-radius: 18px;
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    position: relative;
    animation: fadeIn 0.2s ease;
  }

  .message-in {
    align-self: flex-start;
    background: #fff;
  }

  .message-out {
    align-self: flex-end;
    background: var(--chat-primary);
    color: #fff;
  }

  .message-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.4rem;
    opacity: 0.7;
  }

  .message-text {
    font-size: 0.95rem;
    line-height: 1.5;
    word-break: break-word;
  }

  .message-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    align-items: center;
    font-size: 0.75rem;
    margin-top: 0.5rem;
    opacity: 0.85;
  }

  .message-out .message-footer {
    color: rgba(255, 255, 255, 0.9);
  }

  .message-status {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
  }

  .message-status::before {
    content: '\2713';
  }

  .message-status.is-read::before {
    content: '\2713\2713';
  }

  .message-attachment {
    margin-bottom: 0.5rem;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: rgba(255, 255, 255, 0.25);
  }

  .message-attachment a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 0.85rem;
    color: inherit;
    text-decoration: none;
  }

  .message-attachment--image {
    padding: 0;
    border: none;
  }

  .message-attachment--image img {
    display: block;
    max-width: 260px;
    border-radius: 16px;
  }

  .attachment-icon {
    font-size: 1.25rem;
  }

  .chat-composer {
    border-top: 1px solid var(--chat-border);
    padding-top: 1rem;
    flex: 0 0 auto;
    background-color: transparent; /* antes #ffffff */
  }

  .chat-composer form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .composer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
  }

  .composer-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .composer-btn {
    border: none;
    background: rgba(148, 163, 184, 0.15);
    border-radius: 999px;
    padding: 0.45rem 0.7rem;
    font-size: 1rem;
    cursor: pointer;
  }

  .composer-btn.is-recording {
    background: rgba(248, 113, 113, 0.2);
    color: #b91c1c;
  }

  .chat-input {
    border: none;
    background: var(--chat-primary-soft);
    border-radius: 18px;
    padding: 0.85rem 1.25rem;
    font-size: 0.95rem;
    outline: none;
    resize: none;
    min-height: 52px;
  }

  .chat-input:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
  }

  .chat-send {
    border: none;
    border-radius: 999px;
    padding: 0.85rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    background: linear-gradient(135deg, #2563eb, #9333ea);
    color: #fff;
    letter-spacing: 0.08em;
    box-shadow: 0 15px 30px rgba(79, 70, 229, 0.35);
  }

  .chat-send:disabled {
    opacity: 0.6;
    box-shadow: none;
  }

  .chat-error {
    display: none;
    margin-top: 0.75rem;
    background: rgba(248, 113, 113, 0.1);
    color: #b91c1c;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    font-size: 0.85rem;
  }

  .chat-app__aside {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 0;
    max-height: 100%;
    overflow-y: auto;
  }

  .chat-app__aside .card {
    border: none;
    border-radius: 20px;
    background: linear-gradient(145deg, #fff, rgba(241, 245, 249, 0.9));
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 992px) {
    .chat-app {
      border-radius: 0;
      padding: 1rem 0;
      height: auto;
      max-height: none;
    }

    .chat-app__surface {
      grid-template-columns: 1fr;
      min-height: auto;
    }

    .chat-app__aside {
      order: -1;
      max-height: none;
      overflow: visible;
    }

    .message-bubble {
      max-width: 90%;
    }

    /* un poco menos de radio en mobile */
    .chat-app__main {
      border-radius: 20px;
    }
  }

  .emoji-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-top: 0.5rem;
    background: rgba(148, 163, 184, 0.12);
    border-radius: 12px;
    padding: 0.5rem;
  }

  .emoji-option {
    border: none;
    background: transparent;
    font-size: 1.25rem;
  }

  .chat-quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
  }

  .quick-reply {
    border: 1px solid var(--chat-border);
    border-radius: 999px;
    padding: 0.35rem 0.85rem;
    background: rgba(15, 23, 42, 0.02);
    font-size: 0.85rem;
  }

  .attachment-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: rgba(34, 197, 94, 0.15);
    font-size: 0.85rem;
  }

  .attachment-remove {
    border: none;
    background: transparent;
    font-size: 1rem;
    line-height: 1;
  }

  .recording-indicator {
    font-size: 0.85rem;
    color: #dc2626;
    font-weight: 600;
  }

  .chat-load-more {
    border: 1px dashed var(--chat-border);
    border-radius: 999px;
    padding: 0.4rem 1.25rem;
    align-self: center;
    background: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    font-size: 0.85rem;
  }

  .chat-load-more[hidden] {
    display: none !important;
  }

  .message-bubble--highlight {
    animation: pulse 0.4s ease;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .message-attachment audio,
  .message-attachment video {
    width: 100%;
    display: block;
  }

  .chat-search-results__empty {
    cursor: default;
    color: var(--chat-muted);
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @keyframes pulse {
    from { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.25); }
    to { box-shadow: 0 0 0 12px transparent; }
  }

  /* ================== MODO OSCURO: chat ================== */
  .dark .chat-app {
    background:
      radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.18), transparent 45%),
      radial-gradient(circle at 80% 0%, rgba(16, 185, 129, 0.18), transparent 40%),
      var(--chat-bg);
    color: #e5e7eb;
  }

  .dark .chat-app__surface {
    background: var(--chat-surface);
    border-color: var(--chat-border);
    box-shadow: 0 25px 60px rgba(15, 23, 42, 0.8);
  }

  .dark .chat-app__main {
    background:
      radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.24), transparent 55%),
      radial-gradient(circle at 100% 0%, rgba(56, 189, 248, 0.18), transparent 55%),
      #020617;
    box-shadow: 0 22px 55px rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(148, 163, 184, 0.35);
  }

  .dark .chat-thread {
    background-color: transparent;
  }

  .dark .chat-thread__empty {
    background: rgba(15, 23, 42, 0.7);
    border-color: rgba(148, 163, 184, 0.6);
  }

  .dark .chat-search {
    background: rgba(15, 23, 42, 0.95);
    border-color: var(--chat-border);
  }

  .dark .chat-search input {
    color: #e5e7eb;
  }

  .dark .chat-search-results {
    background: rgba(15, 23, 42, 0.98);
  }

  .dark .chat-search-results__item:hover {
    background: rgba(37, 99, 235, 0.2);
  }

  .dark .message-in {
    background: #020617;
    border: 1px solid rgba(148, 163, 184, 0.5);
  }

  .dark .message-attachment {
    border-color: rgba(148, 163, 184, 0.5);
    background: rgba(15, 23, 42, 0.9);
  }

  .dark .chat-composer {
    background-color: transparent;
    border-top-color: var(--chat-border);
  }

  .dark .chat-input {
    color: #e5e7eb;
  }

  .dark .composer-btn {
    background: rgba(148, 163, 184, 0.18);
    color: #e5e7eb;
  }

  .dark .composer-btn.is-recording {
    background: rgba(248, 113, 113, 0.2);
    color: #fecaca;
  }

  .dark .chat-app__aside .card {
    background: linear-gradient(145deg, #020617, #020617);
  }

  .dark .emoji-picker {
    background: rgba(15, 23, 42, 0.9);
  }

  .dark .quick-reply {
    background: rgba(15, 23, 42, 0.9);
    border-color: rgba(148, 163, 184, 0.6);
  }

  .dark .chat-load-more {
    background: rgba(15, 23, 42, 0.95);
    border-color: rgba(148, 163, 184, 0.6);
  }

  /* ================== MODO OSCURO: Flatpickr ================== */

  /* fondo sólido en TODOS los contenedores para evitar “parpadeos” blancos */
  .dark .fp-shell .flatpickr-calendar,
  .dark .fp-shell .flatpickr-innerContainer,
  .dark .fp-shell .flatpickr-rContainer,
  .dark .fp-shell .flatpickr-weekdays,
  .dark .fp-shell .flatpickr-days,
  .dark .fp-shell .dayContainer{
    background:#020617 !important;
    border-color:#020617 !important;
  }

  .dark .fp-shell .flatpickr-months{
    background:transparent;
  }

  .dark .fp-shell .flatpickr-months .flatpickr-month{
    color:#e5e7eb;
  }

  /* Mes y año más visibles en dark */
  .dark .fp-shell .flatpickr-months .flatpickr-current-month .cur-month,
  .dark .fp-shell .flatpickr-months .flatpickr-current-month .cur-year{
    color:#f9fafb;
    font-weight:700;
  }

  .dark .fp-shell .flatpickr-months .flatpickr-prev-month,
  .dark .fp-shell .flatpickr-months .flatpickr-next-month{
    color:#cbd5f5;
    fill:#cbd5f5;
  }

  .dark .fp-shell .flatpickr-months .flatpickr-prev-month:hover,
  .dark .fp-shell .flatpickr-months .flatpickr-next-month:hover{
    color:#fff;
    fill:#fff;
  }

  .dark .fp-shell span.flatpickr-weekday{
    color:#9ca3af!important;
  }

  .dark .fp-shell .flatpickr-day{
    color:#e5e7eb;
    border-color:transparent;
    background:#020617;
  }

  .dark .fp-shell .flatpickr-day:hover,
  .dark .fp-shell .flatpickr-day:focus{
    background:#020617;
    border-color:#111827;
  }

  .dark .fp-shell .flatpickr-day.today{
    border-color:#e5e7eb;
    color:#e5e7eb;
  }

  .dark .fp-shell .flatpickr-day.today:hover{
    background:#e5e7eb;
    border-color:#e5e7eb;
    color:#020617;
  }

  .dark .fp-shell .flatpickr-day.selected,
  .dark .fp-shell .flatpickr-day.startRange,
  .dark .fp-shell .flatpickr-day.endRange{
    background:#3b82f6!important;
    color:#fff!important;
    border-color:#3b82f6!important;
  }

  /* SIN box-shadow para evitar rayas blancas al mover rápido el cursor */
  .dark .fp-shell .flatpickr-day.inRange{
    background:#0b1120!important;
    border-color:#0b1120!important;
    box-shadow:none !important;
  }

  .dark .fp-shell .flatpickr-day.flatpickr-disabled,
  .dark .fp-shell .flatpickr-day.flatpickr-disabled:hover,
  .dark .fp-shell .flatpickr-day.disabled,
  .dark .fp-shell .flatpickr-day.disabled:hover{
    background-color:#0f172a !important;
    color:#6b7280 !important;
    border-color:transparent !important;
  }

  .dark .fp-shell .flatpickr-day.flatpickr-disabled.inRange,
  .dark .fp-shell .flatpickr-day.disabled.inRange{
    background-color:#020617 !important;
    color:#6b7280 !important;
    box-shadow:none !important;
  }

  @media (min-width:640px){
    /* sin línea divisoria clara entre meses en dark */
    .dark .fp-shell .flatpickr-days .dayContainer:nth-child(1){
      border-right:none !important;
    }
  }

  /* ============ MODO OSCURO: modales de reserva del CHAT ============ */

  .dark #chat-reservation-modal > div {
    background-color:#020617 !important;
    color:#e5e7eb;
    border:1px solid #1f2937;
  }

  /* Resumen de reserva dentro del modal */
  .dark #chat-priceSummary,
  .dark #priceSummary {
    background-color:#020617 !important;
    border-color:#1f2937 !important;
    color:#e5e7eb !important;
  }

  .dark #chat-priceSummary p,
  .dark #priceSummary p {
    color:#e5e7eb !important;
  }

  .dark #chat-reservation-modal .text-muted {
    color:#9ca3af !important;
  }

  .dark #chat-reservation-modal .border {
    border-color:#1f2937 !important;
  }

  .dark #chat-reservation-modal input.form-control {
    background-color:#020617;
    color:#e5e7eb;
    border-color:#1f2937;
  }

  .dark #chat-reservation-modal input.form-control::placeholder {
    color:#6b7280;
  }

  .dark #chat-reservation-modal .btn-outline-secondary {
    color:#e5e7eb;
    border-color:#4b5563;
  }

  .dark #chat-reservation-modal .btn-outline-secondary:hover {
    background-color:#111827;
    border-color:#6b7280;
  }

  /* ================= AJUSTES EXTRA MODO OSCURO ================= */

  .dark .chat-app .text-muted {
    color: #9ca3af !important;
  }

  .dark .chat-thread__empty,
  .dark .chat-thread__empty p,
  .dark .chat-thread__empty .fw-semibold {
    color: #e5e7eb !important;
  }

  .dark .chat-app__aside .card,
  .dark .chat-app__aside .card-body,
  .dark .chat-app__aside .card-header {
    color: #e5e7eb !important;
  }

  .dark .chat-app__aside .card-header.bg-white {
    background-color: transparent !important;
    color: #e5e7eb !important;
    border-bottom: 1px solid rgba(148, 163, 184, 0.5);
  }

  .dark .chat-app__aside .card {
    background:
      radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.22), transparent 60%),
      #020617;
    border: 1px solid rgba(148, 163, 184, 0.35);
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.9);
  }

  html.theme-fade * {
    transition:
      background-color .35s ease,
      color .35s ease,
      border-color .35s ease,
      fill .35s ease;
  }

  #theme-toggle {
    transition:
      background-color .25s ease,
      color .25s ease,
      transform .25s ease,
      box-shadow .25s ease;
  }

  #theme-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.55);
  }

  #theme-toggle.theme-bounce {
    transform: translateY(-1px) scale(1.04);
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.75);
  }

  #theme-toggle-icon {
    transition: transform .35s ease;
  }

  #theme-toggle-icon.theme-spin {
    transform: rotate(180deg);
  }

  .dark #theme-toggle {
    background-color: #020617 !important;
    color: #e5e7eb !important;
  }
</style>
