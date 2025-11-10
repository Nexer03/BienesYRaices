<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /** Inbox: lista de conversaciones (para agente y cliente) */
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::with([
                'property:id,title',
                'agent:id,name,avatar',
                'client:id,name,avatar',
            ])
            ->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                  ->orWhere('client_id', $user->id);
            })
            ->withCount([
                'messages as unread_count' => function ($q) use ($user) {
                    $q->whereNull('read_at')
                      ->where('sender_id', '!=', $user->id);
                }
            ])
            ->latest('updated_at')
            ->paginate(20);

        // 👉 si es agente usa la vista agent.chats.index, si no chat.index
        $view = ($user->role === 'agent') ? 'agent.chats.index' : 'chat.index';
        return view($view, compact('conversations', 'user'));
    }

    /** Cliente abre chat desde una propiedad (crea si no existe) */
    public function show(Property $property)
    {
        $agentId = $property->user_id;
        $authId  = Auth::id();
        $user    = Auth::user();

        $conversation = Conversation::firstOrCreate([
            'property_id' => $property->id,
            'agent_id'    => $agentId,
            'client_id'   => $authId,
        ]);

        // marcar como leídos los del otro
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $authId)
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()->with('sender:id,name,avatar')->get();

        // 👉 vista por rol (agente ve agent.chats.show; cliente ve chat.show)
        $view = ($user->role === 'agent') ? 'agent.chats.show' : 'chat.show';
        return view($view, compact('conversation', 'messages', 'property'));
    }

    /** Abrir chat por ID de conversación (agente o cliente) */
    public function open(Conversation $conversation)
    {
        $user = Auth::user();
        $userId = $user->id;

        // seguridad: debe pertenecer a la conversación
        abort_unless(in_array($userId, [$conversation->agent_id, $conversation->client_id]), 403);

        // marcar como leídos los del otro
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->update(['read_at' => now()]);

        $conversation->load(['property:id,title', 'agent:id,name,avatar', 'client:id,name,avatar']);
        $messages = $conversation->messages()->with('sender:id,name,avatar')->get();
        $property = $conversation->property;

        // 👉 vista por rol
        $view = ($user->role === 'agent') ? 'agent.chats.show' : 'chat.show';
        return view($view, compact('conversation', 'messages', 'property'));
    }

    /** Enviar mensaje (agente o cliente) */
    public function send(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();

        // seguridad: debe pertenecer a la conversación
        abort_unless(in_array($userId, [$conversation->agent_id, $conversation->client_id]), 403);

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'body'      => $data['body'],
        ]);

        // actualiza orden en inbox
        $conversation->touch();

        $recipientId = $conversation->agent_id === $userId
            ? $conversation->client_id
            : $conversation->agent_id;

        if ($recipientId && $recipientId !== $userId) {
            $recipient = User::find($recipientId);
            if ($recipient) {
                $recipient->notify(new NewMessageNotification($message));
            }
        }

        // broadcast opcional (cuando conectes Pusher/Echo)
        // broadcast(new \App\Events\MessageSent($conversation, $message))->toOthers();

        return response()->json($message->load('sender:id,name,avatar'));
    }
}
