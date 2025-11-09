@extends('layouts.app')

@section('content')
<div class="container">
  <h4>Chat con el agente de "{{ $property->title }}"</h4>
  <div id="messages" style="height:300px; overflow:auto; border:1px solid #ccc; padding:8px;">
    @foreach($messages as $msg)
      <div><strong>{{ $msg->sender->name }}:</strong> {{ $msg->body }}</div>
    @endforeach
  </div>

  <form id="form" method="POST" action="{{ route('chat.send', $conversation) }}">
    @csrf
    <input id="body" name="body" placeholder="Escribe un mensaje..." class="form-control mb-2" />
    <button class="btn btn-primary">Enviar</button>
  </form>
</div>

<script>
const form = document.getElementById('form');
form.addEventListener('submit', async e => {
  e.preventDefault();
  const body = document.getElementById('body').value;
  if (!body) return;
  const res = await fetch(form.action, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    body: JSON.stringify({ body })
  });
  const msg = await res.json();
  document.getElementById('messages').innerHTML += `<div><b>${msg.sender.name}:</b> ${msg.body}</div>`;
  document.getElementById('body').value = '';
});
</script>
@endsection
