@php
  $authUser = auth()->user();
  $isAgent  = $authUser && $conversation->agent_id === $authUser->id;
  $isClient = $authUser && $conversation->client_id === $authUser->id;

  if ($isAgent) {
      // Quick replies pensados para el AGENTE
      $quickReplies = [
          'Puedo agendarte una visita 🗓️',
          '¿Qué día y horario te acomoda mejor para la visita? ⏰',
          'Te explico cómo funciona el proceso de compra. 📝',
          'Puedo enviarte más fotos o un video de la propiedad. 📷',
          'Te ayudo a resolver cualquier duda que tengas. 🙂',
      ];
  } elseif ($isClient) {
      // Quick replies pensados para el CLIENTE
      $quickReplies = [
          'Me interesa agendar una visita ✅',
          '¿Está disponible en estas fechas? 📅',
          '¿Puedes compartir más fotos o video? 📷',
          'Gracias por la información, lo revisaré. 🙏',
          '¿Hay algún costo extra que deba considerar? 💸',
      ];
  } else {
      // Fallback por si en algún momento entra alguien que no es ni agente ni cliente
      $quickReplies = [
          'Gracias por la información. 🙏',
          '¿Puedes compartir más detalles? 📝',
      ];
  }

  $hasMore  = $hasMoreMessages ?? false;
  $oldestId = $oldestMessageId ?? null;
@endphp

<div class="chat-app">
  <div class="chat-app__surface">
    <div class="chat-app__main">
      {{-- HEADER --}}
      <div class="chat-app__header">
        <div class="chat-partner">
          <div class="chat-partner__avatar">
            {{ mb_strtoupper(mb_substr($otro->name ?? 'U', 0, 1)) }}
          </div>
          <div>
            <p class="chat-partner__label">Conversación con</p>
            <h5 class="mb-0">{{ $otro->name ?? 'Usuario' }}</h5>

            @if(isset($property))
              <small class="text-muted">{{ $property->title }}</small>
            @endif
          </div>
        </div>

        <div class="chat-status" aria-live="polite">
          <span class="status-dot" id="chat-presence-dot"></span>
          <span id="chat-typing-text">En línea recientemente</span>
        </div>
      </div>


      {{-- HILO DE MENSAJES --}}
      <div
        class="chat-thread"
        id="messages"
        data-conversation="{{ $conversation->id }}"
        data-me="{{ $yo }}"
        data-me-name="{{ auth()->user()->name ?? 'Tú' }}"
        data-other="{{ $otro->id ?? '' }}"
        data-other-name="{{ $otro->name ?? 'Usuario' }}"
        data-read-url="{{ route('chat.read', $conversation) }}"
        data-messages-url="{{ route('chat.messages', $conversation) }}"
        data-has-more="{{ $hasMore ? '1' : '0' }}"
        data-oldest-id="{{ $oldestId ?? '' }}"
        data-visit-panel-url="{{ route('chat.side-panel', $conversation) }}"
      >
        <button
          type="button"
          id="chat-load-more"
          class="chat-load-more"
          @if(!$hasMore) hidden @endif
        >
          Cargar mensajes anteriores
        </button>

        @forelse ($messages as $msg)
          @php
            $isMine        = $msg->sender_id === $yo;
            $type          = $msg->attachment_type;
            $isImage       = $type && str_starts_with($type, 'image/');
            $isAudio       = $type && str_starts_with($type, 'audio/');
            $isVideo       = $type && str_starts_with($type, 'video/');
            $hasAttachment = !empty($msg->attachment_url);
          @endphp

          <div
            class="message-bubble {{ $isMine ? 'message-out' : 'message-in' }}"
            data-message-id="{{ $msg->id }}"
            @if($isMine)
              data-message-status="{{ $msg->read_at ? 'read' : 'sent' }}"
            @endif
          >
            <div class="message-meta">
              <span class="message-author">
                {{ $isMine ? 'Tú' : ($msg->sender->name ?? 'Usuario') }}
              </span>
              <span class="message-time">
                {{ optional($msg->created_at)->timezone(config('app.timezone'))->format('H:i') }}
              </span>
            </div>

            @if($hasAttachment)
              <div class="message-attachment {{ $isImage ? 'message-attachment--image' : '' }}">
                @if($isImage)
                  <a href="{{ $msg->attachment_url }}" target="_blank" rel="noopener" aria-label="Abrir adjunto">
                    <img src="{{ $msg->attachment_url }}" alt="Adjunto" loading="lazy">
                  </a>
                @elseif($isAudio)
                  <audio controls preload="none">
                    <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_type }}">
                    Tu navegador no soporta audio embebido.
                  </audio>
                @elseif($isVideo)
                  <video controls preload="metadata">
                    <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_type }}">
                    Tu navegador no soporta video embebido.
                  </video>
                @else
                  <a href="{{ $msg->attachment_url }}" target="_blank" rel="noopener">
                    <span class="attachment-icon">📎</span>
                    <span>{{ $msg->attachment_name ?? 'Archivo adjunto' }}</span>
                  </a>
                @endif
              </div>
            @endif

            @if(filled($msg->body))
              <div class="message-text">{{ $msg->body }}</div>
            @endif

            <div class="message-footer">
              <span class="message-time">
                {{ optional($msg->created_at)->timezone(config('app.timezone'))->format('d/m H:i') }}
              </span>

              @if($isMine)
                <span class="message-status {{ $msg->read_at ? 'is-read' : '' }}">
                  {{ $msg->read_at ? 'Visto' : 'Enviado' }}
                </span>
              @endif
            </div>
          </div>
        @empty
          <div class="chat-thread__empty">
            <p class="mb-1 fw-semibold">Rompe el hielo 🧊</p>
            <p class="mb-0 text-muted">Aún no hay mensajes en esta conversación.</p>
          </div>
        @endforelse
      </div>

      {{-- COMPOSER / CAJA DE MENSAJES --}}
      <div class="chat-composer">
        <form
          id="chat-form"
          method="POST"
          action="{{ route('chat.send', $conversation) }}"
          enctype="multipart/form-data"
        >
          @csrf

          <div class="composer-row">
            <div class="composer-actions">
              <button
                class="composer-btn"
                type="button"
                id="emoji-toggle"
                aria-label="Insertar emoji"
              >
                😊
              </button>

              <button
                class="composer-btn"
                type="button"
                id="attachment-btn"
                aria-label="Adjuntar archivo"
              >
                📎
              </button>

              <button
                class="composer-btn"
                type="button"
                id="record-audio-btn"
                aria-label="Grabar nota de voz"
              >
                🎙️
              </button>

              <span id="recording-indicator" class="recording-indicator" hidden>
                Grabando…
              </span>

              <input
                id="chat-attachment"
                name="attachment"
                type="file"
                class="visually-hidden"
                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,audio/*,video/*"
              >

              <div id="chat-attachment-chip" class="attachment-chip" hidden>
                <span id="chat-attachment-name"></span>
                <button
                  type="button"
                  class="attachment-remove"
                  id="chat-attachment-remove"
                  aria-label="Quitar adjunto"
                >
                  &times;
                </button>
              </div>
            </div>

            <button id="chat-send" type="submit" class="chat-send">
              Enviar
            </button>
          </div>

          <div class="composer-body">
            <textarea
              id="chat-body"
              name="body"
              class="chat-input"
              placeholder="Escribe un mensaje..."
              rows="1"
              autocomplete="off"
            ></textarea>

            <div id="emoji-picker" class="emoji-picker" hidden>
              @foreach(['😀','😄','😍','🤩','🙏','👍','🎉','🏡'] as $emoji)
                <button
                  type="button"
                  class="emoji-option"
                  data-emoji="{{ $emoji }}"
                >
                  {{ $emoji }}
                </button>
              @endforeach
            </div>
          </div>
        </form>

        <div class="chat-quick-replies">
          @foreach($quickReplies as $reply)
            <button
              type="button"
              class="quick-reply"
              data-quick-reply="{{ $reply }}"
            >
              {{ $reply }}
            </button>
          @endforeach
        </div>

        <div id="chat-error" class="chat-error" role="alert"></div>
      </div>
    </div>

    {{-- PANEL LATERAL --}}
    <aside
      id="chat-visit-panel"
      class="chat-app__aside"
    >
      @include('chat.partials.side-panel', [
          'conversation'      => $conversation,
          'property'          => $property ?? null,
          'nextVisit'         => $nextVisit ?? null,
          'activeReservation' => $activeReservation ?? null,
          'authUser'          => auth()->user(),
      ])
    </aside>

  </div>
</div>

<button
  id="chat-theme-toggle"
  class="chat-theme-toggle"
  type="button"
  aria-label="Cambiar tema"
>
  <i id="chat-theme-toggle-icon" class="fa-solid chat-theme-toggle__icon"></i>
  <span class="chat-theme-toggle__label">Tema</span>
</button>
