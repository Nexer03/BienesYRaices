<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - SIN BECA NO HAY RENTA</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    {{-- Iconos (opcional) --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    {{-- Header principal --}}
    <x-main-header />

    {{-- Contenido principal --}}
    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                Mensajes
            </h1>
            <p class="text-sm text-gray-600">
                Consulta tus conversaciones con agentes y clientes.
            </p>
        </header>

        @if ($conversations->isEmpty())
            <div class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white px-6 py-8 text-center">
                <div class="mb-2 text-gray-500">
                    <i class="fa-regular fa-comments text-3xl mb-2"></i>
                </div>
                <p class="text-gray-600">
                    No tienes conversaciones todavía.
                </p>
            </div>
        @else
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <ul class="divide-y divide-gray-100">
                    @foreach ($conversations as $c)
                        @php
                            $isAgent = (auth()->id() === $c->agent_id);
                            $other   = $isAgent ? $c->client : $c->agent;
                        @endphp

                        <li>
                            <a href="{{ route('chat.open', $c) }}"
                               class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-gray-50 transition">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $other->name ?? 'Usuario' }}
                                        </p>
                                        @if($c->property)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-700">
                                                <i class="fa-solid fa-house-chimney text-[10px]"></i>
                                                {{ Str::limit($c->property->title, 40) }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        {{ $isAgent ? 'Eres el agente' : 'Eres el cliente' }}
                                    </p>
                                </div>

                                @if(($c->unread_count ?? 0) > 0)
                                    <span class="shrink-0 inline-flex items-center justify-center rounded-full
                                                 bg-blue-600 text-white text-xs font-semibold px-2 py-1">
                                        {{ $c->unread_count }} nuevo{{ $c->unread_count > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $conversations->links() }}
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <x-main-footer />

</body>
</html>
