<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Mensajes del Agente</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800">

  {{-- HEADER GLOBAL --}}
  <x-main-header />

  <main class="max-w-5xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-3xl font-semibold text-gray-900 flex items-center gap-2">
        <i class="fa-solid fa-comments text-blue-600"></i> Mensajes (Agente)
      </h2>
      <a href="{{ url()->previous() }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
        <i class="fa-solid fa-arrow-left"></i> Volver
      </a>
    </div>

    {{-- MENSAJE FLASH --}}
    @if (session('info'))
      <div class="mb-6 p-4 rounded-lg border border-blue-200 bg-blue-50 text-blue-800">
        <i class="fa-solid fa-circle-info mr-2"></i> {{ session('info') }}
      </div>
    @endif

    {{-- SIN CONVERSACIONES --}}
    @if ($conversations->isEmpty())
      <div class="text-center py-10 bg-white border border-gray-200 rounded-xl shadow-sm">
        <i class="fa-regular fa-comments text-5xl text-gray-300 mb-3"></i>
        <p class="text-gray-600 text-lg">Aún no tienes conversaciones con clientes.</p>
      </div>

    {{-- CONVERSACIONES --}}
    @else
      <div class="space-y-3">
        @foreach ($conversations as $c)
          @php
            $other = $c->client; // desde vista del agente, el “otro” es el cliente
          @endphp

          <a href="{{ route('chat.open', $c) }}"
             class="flex items-center justify-between w-full rounded-lg border border-gray-200 bg-white p-4 hover:bg-gray-50 transition shadow-sm">
            <div class="min-w-0 flex-1">
              <div class="font-semibold text-gray-800 truncate">
                <i class="fa-solid fa-user text-gray-500 mr-2"></i>
                {{ $other->name ?? 'Cliente' }}
                @if($c->property)
                  <span class="text-gray-500 font-normal">— {{ $c->property->title }}</span>
                @endif
              </div>
              <div class="text-sm text-gray-500 mt-0.5">
                Conversación con el cliente
              </div>
            </div>

            @if(($c->unread_count ?? 0) > 0)
              <span class="ml-3 inline-flex items-center justify-center rounded-full text-xs font-semibold bg-blue-600 text-white px-3 py-1.5 shadow-sm">
                {{ $c->unread_count }}
              </span>
            @endif
          </a>
        @endforeach
      </div>

      <div class="mt-6">
        {{ $conversations->links() }}
      </div>
    @endif
  </main>

  {{-- FOOTER GLOBAL --}}
  <x-main-footer />

</body>
</html>
