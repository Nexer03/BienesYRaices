@extends('layouts.app')

@section('content')
@php
  $yo = auth()->id();
  $otro = $yo === $conversation->agent_id ? $conversation->client : $conversation->agent;
@endphp

{{-- Estilos específicos del chat (incluye modo oscuro y Tailwind-friendly) --}}
@include('chat.partials.styles')

{{-- Flatpickr para calendario de reservas en el chat --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Frame principal del chat (layout + mensajes + panel lateral) --}}
@include('chat.partials.frame', [
  'conversation' => $conversation,
  'messages' => $messages,
  'property' => $property ?? null,
  'nextVisit' => $nextVisit ?? null,
  'activeReservation' => $activeReservation ?? null,
  'yo' => $yo,
  'otro' => $otro,
  'hasMoreMessages' => $hasMoreMessages ?? false,
  'oldestMessageId' => $oldestMessageId ?? null,
])

{{-- Lógica JS del chat en tiempo real --}}
@include('chat.partials.script', ['conversation' => $conversation])
<div class="flex flex-col bg-slate-950/90 border border-slate-800 rounded-3xl ...">

{{-- Toggle de tema sincronizado con localStorage --}}
<script>
  
  (function () {
    const html  = document.documentElement;
    const btn   = document.getElementById('theme-toggle');
    const icon  = document.getElementById('theme-toggle-icon') || btn?.querySelector('i');
    const label = btn?.querySelector('span');

    function setIconAndLabel() {
      const isDark = html.classList.contains('dark');
      if (!icon || !label) return;

      icon.classList.remove('fa-sun', 'fa-moon');
      icon.classList.add(isDark ? 'fa-moon' : 'fa-sun');

      // Puedes elegir qué texto mostrar: lo que eres o lo que hará
      // Aquí muestro lo que HARÁ al pulsar:
      label.textContent = isDark ? 'Modo claro' : 'Modo oscuro';
    }

    function apply(mode) {
      if (mode !== 'dark' && mode !== 'light') return;
      const isDark = mode === 'dark';

      html.classList.toggle('dark', isDark);
      try {
        localStorage.setItem('theme', mode);
      } catch (e) {}
      setIconAndLabel();
    }

    // 1) Al entrar a la vista, lee el valor guardado y aplícalo
    (function initFromStorage() {
      let stored = null;
      try {
        stored = localStorage.getItem('theme');
      } catch (e) {}

      if (stored === 'dark' || stored === 'light') {
        apply(stored);              // aquí forzamos el mismo tema que la vista anterior
      } else {
        setIconAndLabel();          // fallback: solo ajusta icono y texto
      }
    })();

    // 2) Al hacer clic, alterna y guarda
    btn?.addEventListener('click', () => {
      const next = html.classList.contains('dark') ? 'light' : 'dark';
      apply(next);
    });
  })();
</script>
@endsection
