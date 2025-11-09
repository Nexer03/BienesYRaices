<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Mostrar conversación (crear si no existe)
    public function show(Property $property)
    {
        $agentId = $property->user_id;
        $clientId = Auth::id();

        $conversation = Conversation::firstOrCreate([
            'property_id' => $property->id,
            'agent_id' => $agentId,
            'client_id' => $clientId,
        ]);

        $messages = $conversation->messages()->with('sender:id,name,avatar')->get();

        return view('chat.show', compact('conversation', 'messages', 'property'));
    }

    // Enviar mensaje
    public function send(Request $request, Conversation $conversation)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        // Evento de broadcast aquí (se añade después con Pusher)
        broadcast(new \App\Events\MessageSent($conversation, $message))->toOthers();

        return response()->json($message->load('sender:id,name,avatar'));
    }
}
