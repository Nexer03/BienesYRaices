<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversation}', function ($user, Conversation $conversation) {
    if (in_array($user->id, [$conversation->agent_id, $conversation->client_id])) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});

Broadcast::channel('presence.conversation.{conversation}', function ($user, Conversation $conversation) {
    if (in_array($user->id, [$conversation->agent_id, $conversation->client_id])) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});
