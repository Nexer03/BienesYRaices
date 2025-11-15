<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Visit;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public ?Visit $visit,
        public string $action // created, updated, confirmed, cancelled
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversation.' . $this->conversation->id)];
    }

    public function broadcastAs(): string
    {
        return 'VisitUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'visit'  => $this->visit ? [
                'id'         => $this->visit->id,
                'status'     => $this->visit->status,
                'visit_date' => $this->visit->visit_date?->toIso8601String(),
                'notes'      => $this->visit->notes,
            ] : null,
        ];
    }
}
