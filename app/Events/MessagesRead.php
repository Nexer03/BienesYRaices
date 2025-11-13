<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  int    $conversationId   ID de la conversación
     * @param  array  $messageIds       IDs de mensajes marcados como leídos
     * @param  int    $readerId         Usuario que los leyó
     */
    public function __construct(
        public int $conversationId,
        public array $messageIds,
        public int $readerId,
    ) {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    /**
     * 👈 Nombre EXACTO que escucha tu JS:
     * .listen('MessagesRead', ...)
     */
    public function broadcastAs(): string
    {
        return 'MessagesRead';
    }

    public function broadcastWith(): array
    {
        return [
            'message_ids' => $this->messageIds,
            'reader_id'   => $this->readerId,
        ];
    }
}
