@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h2 class="text-2xl font-semibold mb-4">Mensajes</h2>

  @if ($conversations->isEmpty())
    <div class="text-gray-600">No tienes conversaciones todavía.</div>
  @else
    <div class="space-y-2">
      @foreach ($conversations as $c)
        @php
          $isAgent = (auth()->id() === $c->agent_id);
          $other   = $isAgent ? $c->client : $c->agent;
        @endphp

        <a href="{{ route('chat.open', $c) }}"
           class="flex items-center justify-between w-full rounded-lg border p-4 hover:bg-gray-50 transition">
          <div class="min-w-0">
            <div class="font-semibold truncate">
              {{ $other->name ?? 'Usuario' }}
              @if($c->property)
                <span class="text-gray-500 font-normal">— {{ $c->property->title }}</span>
              @endif
            </div>
            <div class="text-sm text-gray-500">
              {{ $isAgent ? 'Eres el agente' : 'Eres el cliente' }}
            </div>
          </div>

          @if(($c->unread_count ?? 0) > 0)
            <span class="ml-3 inline-flex items-center justify-center rounded-full text-xs font-semibold bg-blue-600 text-white px-2 py-1">
              {{ $c->unread_count }}
            </span>
          @endif
        </a>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $conversations->links() }}
    </div>
  @endif
</div>
@endsection
