<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  int    $conversationId  ID de la conversación para el canal privado
     * @param  Message $message        Mensaje recién creado
     */
    public function __construct(
        public int $conversationId,
        public Message $message,
    ) {
        // Cargamos el remitente para usarlo en el front
        $this->message->loadMissing('sender:id,name,avatar');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    /**
     * 👈 Nombre EXACTO que escucha tu JS:
     * .listen('MessageSent', ...)
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Payload que recibes en JS como "event.message"
     */
    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'message' => [
                'id'         => $this->message->id,
                'body'       => $this->message->body,
                'read_at'    => optional($this->message->read_at)->toIso8601String(),
                'attachment' => $this->serializeAttachment(),
                'sender'     => $sender ? [
                    'id'     => $sender->id,
                    'name'   => $sender->name,
                    'avatar' => $sender->avatar,
                ] : null,
                'created_at' => optional($this->message->created_at)->toIso8601String(),
            ],
        ];
    }

    protected function serializeAttachment(): ?array
    {
        if (!$this->message->attachment_path) {
            return null;
        }

        return [
            'name' => $this->message->attachment_name,
            'type' => $this->message->attachment_type,
            'url'  => Storage::disk('public')->url($this->message->attachment_path),
        ];
    }
}
