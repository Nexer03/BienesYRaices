@extends('layouts.app')

@section('content')
<div class="container" style="position:relative; z-index: 10;">
  @php
    $yo   = auth()->id();
    $otro = $yo === $conversation->agent_id ? $conversation->client : $conversation->agent;
  @endphp

  <h4 class="mb-3">
    Chat (Agente) con {{ $otro->name ?? 'Usuario' }}
    @if(isset($property)) <small class="text-muted"> · {{ $property->title }}</small> @endif
  </h4>

  <div id="messages"
       style="height:320px; overflow:auto; border:1px solid #ddd; border-radius:8px; padding:10px; background:#fafafa;">
    @forelse ($messages as $msg)
      <div class="mb-2">
        <strong>{{ $msg->sender->name ?? 'Usuario' }}:</strong> {{ $msg->body }}
      </div>
    @empty
      <div class="text-muted">Aún no hay mensajes.</div>
    @endforelse
  </div>

  <form id="chat-form"
        method="POST"
        action="{{ route('chat.send', $conversation) }}"
        class="mt-3 d-flex gap-2"
        style="pointer-events:auto;">
    @csrf
    <input id="chat-body"
           name="body"
           type="text"
           class="form-control"
           placeholder="Escribe un mensaje..."
           autocomplete="off"
           required>
    <button id="chat-send"
            type="submit"
            class="btn btn-primary">
      Enviar
    </button>
  </form>

  <div id="chat-error" class="mt-2 text-danger" style="display:none;"></div>
</div>

<script>
(function () {
  const messagesEl = document.getElementById('messages');
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-body');
  const sendBtn = document.getElementById('chat-send');
  const errBox = document.getElementById('chat-error');

  const scrollBottom = () => { messagesEl.scrollTop = messagesEl.scrollHeight; };
  scrollBottom();

  const setSending = (state) => {
    sendBtn.disabled = state;
    sendBtn.textContent = state ? 'Enviando...' : 'Enviar';
  };

  const appendMessage = (msg) => {
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `<strong>${(msg.sender && msg.sender.name) ? msg.sender.name : 'Tú'}:</strong> ${msg.body}`;
    messagesEl.appendChild(div);
    scrollBottom();
  };

  const showError = (text) => {
    errBox.textContent = text;
    errBox.style.display = 'block';
    setTimeout(() => { errBox.style.display = 'none'; }, 3500);
  };

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = input.value.trim();
    if (!body) return;

    setSending(true);
    errBox.style.display = 'none';

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ body })
      });

      if (!res.ok) {
        if (res.status === 419) showError('Sesión expirada. Recarga la página.');
        else if (res.status === 403) showError('No autorizado para esta conversación.');
        else showError('Error al enviar (' + res.status + ').');
        setSending(false);
        return;
      }

      const msg = await res.json();
      appendMessage(msg);
      input.value = '';
    } catch (err) {
      console.error(err);
      showError('No se pudo conectar con el servidor.');
    } finally {
      setSending(false);
      input.focus();
    }
  });
})();
</script>
@endsection
