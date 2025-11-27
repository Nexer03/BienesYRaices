<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message)
    {
        $this->message->loadMissing(['sender', 'conversation.property']);
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $conversation = $this->message->conversation;
        $property = $conversation?->property;
        $sender = $this->message->sender;

        return [
            'message_id' => $this->message->id,
            'conversation_id' => $conversation?->id,
            'sender_id' => $sender?->id,
            'sender_name' => $sender?->name,
            'property_id' => $property?->id,
            'property_title' => $property?->title,
            'preview' => Str::limit($this->message->body, 120),
        ];
    }
}
