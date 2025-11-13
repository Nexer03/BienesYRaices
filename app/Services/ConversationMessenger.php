<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\UploadedFile;

class ConversationMessenger
{
    /**
     * Crea un mensaje dentro de la conversación, notifica al receptor
     * y emite el evento de broadcast en tiempo real.
     */
    public function send(Conversation $conversation, int $senderId, ?string $body = null, ?UploadedFile $attachment = null): Message
    {
        abort_unless(in_array($senderId, [$conversation->agent_id, $conversation->client_id]), 403);

        if (!$attachment && (!is_string($body) || trim($body) === '')) {
            abort(422, 'El mensaje no puede estar vacío.');
        }

        $payload = [
            'sender_id' => $senderId,
            'body'      => trim((string) $body),
        ];

        if ($attachment instanceof UploadedFile) {
            $path = $attachment->storePublicly(
                'chat-attachments/' . $conversation->id,
                ['disk' => 'public']
            );

            $payload['attachment_path'] = $path;
            $payload['attachment_name'] = $attachment->getClientOriginalName();
            $payload['attachment_type'] = $attachment->getMimeType();
        }

        $message = $conversation->messages()->create($payload);

        $message->load('sender:id,name,avatar');

        $conversation->touch();

        $recipientId = $conversation->agent_id === $senderId
            ? $conversation->client_id
            : $conversation->agent_id;

        if ($recipientId && $recipientId !== $senderId) {
            $recipient = User::find($recipientId);
            if ($recipient) {
                $recipient->notify(new NewMessageNotification($message));
            }
        }

        broadcast(new MessageSent($conversation->id, $message))->toOthers();

        return $message;
    }
}
