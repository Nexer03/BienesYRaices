<?php

namespace App\Http\Controllers;

use App\Events\MessagesRead;
use App\Models\Conversation;
use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\Visit;
use App\Services\ConversationMessenger;
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

        if ($agentId === $authId) {
            return redirect()
                ->route('properties.show', $property)
                ->with('error', 'No puedes iniciar un chat sobre una propiedad que te pertenece.');
        }

        $conversation = Conversation::firstOrCreate([
            'property_id' => $property->id,
            'agent_id'    => $agentId,
            'client_id'   => $authId,
        ]);

        $this->markConversationMessagesAsRead($conversation, $authId);

        $conversation->loadMissing(['property', 'agent', 'client']);
        [$messages, $hasMoreMessages, $oldestMessageId] = $this->initialMessages($conversation);

        [$propertyDetails, $nextVisit, $activeReservation] = $this->extrasForConversation($conversation);

        // 👉 vista por rol (agente ve agent.chats.show; cliente ve chat.show)
        $view = ($user->role === 'agent') ? 'agent.chats.show' : 'chat.show';
        return view($view, [
            'conversation'       => $conversation,
            'messages'           => $messages,
            'property'           => $propertyDetails,
            'nextVisit'          => $nextVisit,
            'activeReservation'  => $activeReservation,
            'hasMoreMessages'    => $hasMoreMessages,
            'oldestMessageId'    => $oldestMessageId,
        ]);
    }

    /** Abrir chat por ID de conversación (agente o cliente) */
    public function open(Conversation $conversation)
    {
        $user = Auth::user();
        $userId = $user->id;

        // seguridad: debe pertenecer a la conversación
        abort_unless(in_array($userId, [$conversation->agent_id, $conversation->client_id]), 403);

        $this->markConversationMessagesAsRead($conversation, $userId);

        $conversation->load(['property', 'agent:id,name,avatar', 'client:id,name,avatar']);
        [$messages, $hasMoreMessages, $oldestMessageId] = $this->initialMessages($conversation);

        [$property, $nextVisit, $activeReservation] = $this->extrasForConversation($conversation);

        // 👉 vista por rol
        $view = ($user->role === 'agent') ? 'agent.chats.show' : 'chat.show';
        return view($view, [
            'conversation'      => $conversation,
            'messages'          => $messages,
            'property'          => $property,
            'nextVisit'         => $nextVisit,
            'activeReservation' => $activeReservation,
            'hasMoreMessages'   => $hasMoreMessages,
            'oldestMessageId'   => $oldestMessageId,
        ]);
    }

    /** Enviar mensaje (agente o cliente) */
    public function send(Request $request, Conversation $conversation, ConversationMessenger $messenger)
    {
        $userId = Auth::id();

        // seguridad: debe pertenecer a la conversación
        abort_unless(in_array($userId, [$conversation->agent_id, $conversation->client_id]), 403);

        $data = $request->validate([
            'body'       => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:20480|mimetypes:' . implode(',', [
                'image/jpeg', 'image/png', 'image/webp',
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain', 'text/csv',
                'audio/mpeg', 'audio/mp4', 'audio/ogg', 'audio/wav', 'audio/webm',
                'video/mp4', 'video/webm',
            ]),
        ]);

        if (!$request->file('attachment') && blank($data['body'])) {
            return response()->json([
                'message' => 'Escribe algo o adjunta un archivo.',
            ], 422);
        }

        $message = $messenger->send($conversation, $userId, $data['body'] ?? null, $request->file('attachment'));

        return response()->json($message);
    }

    public function markRead(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        abort_unless(in_array($user->id, [$conversation->agent_id, $conversation->client_id]), 403);

        $ids = collect($request->input('message_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['status' => 'noop']);
        }

        $readNow = $conversation->messages()
            ->whereIn('id', $ids)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->pluck('id');

        if ($readNow->isEmpty()) {
            return response()->json(['status' => 'noop']);
        }

        $conversation->messages()->whereIn('id', $readNow)->update(['read_at' => now()]);

        broadcast(new MessagesRead($conversation->id, $readNow->all(), $user->id))->toOthers();

        return response()->json(['status' => 'ok', 'message_ids' => $readNow]);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        abort_unless(in_array($user->id, [$conversation->agent_id, $conversation->client_id]), 403);

        $limit = (int) $request->integer('limit', 30);
        $limit = max(5, min($limit, 100));

        $query = $conversation->messages()->with('sender:id,name,avatar');

        if ($before = (int) $request->input('before')) {
            $query->where('id', '<', $before);
        }

        if ($term = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($term) {
                $q->where('body', 'like', '%' . $term . '%')
                  ->orWhere('attachment_name', 'like', '%' . $term . '%');
            });
        }

        $messages = $query->latest('id')->limit($limit + 1)->get();

        $hasMore = $messages->count() > $limit;
        if ($hasMore) {
            $messages = $messages->take($limit);
        }

        $messages = $messages->sortBy('id')->values();

        return response()->json([
            'messages'    => $messages,
            'has_more'    => $hasMore,
            'next_before' => optional($messages->first())->id,
        ]);
    }

    protected function extrasForConversation(Conversation $conversation): array
    {
        $property = $conversation->property;
        $nextVisit = null;
        $activeReservation = null;

        if ($property) {
            if ($property->listing_type === 'sale') {
                $nextVisit = Visit::where('property_id', $property->id)
                    ->where('agent_id', $conversation->agent_id)
                    ->where('client_id', $conversation->client_id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('visit_date', '>=', now()->subDay())
                    ->orderBy('visit_date')
                    ->first();
            }

            if ($property->listing_type === 'rent') {
                $activeReservation = PropertyReservation::where('property_id', $property->id)
                    ->where('user_id', $conversation->client_id)
                    ->whereNotIn('status', ['cancelled', 'canceled', 'completed'])
                    ->latest('updated_at')
                    ->first();
            }
        }

        return [$property, $nextVisit, $activeReservation];
    }

    protected function markConversationMessagesAsRead(Conversation $conversation, int $userId): void
    {
        $ids = $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $conversation->messages()->whereIn('id', $ids)->update(['read_at' => now()]);

        broadcast(new MessagesRead($conversation->id, $ids->all(), $userId))->toOthers();
    }

    protected function initialMessages(Conversation $conversation, int $limit = 40): array
    {
        $messages = $conversation->messages()
            ->with('sender:id,name,avatar')
            ->latest('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $messages->count() > $limit;
        if ($hasMore) {
            $messages = $messages->take($limit);
        }

        $messages = $messages->sortBy('id')->values();

        return [
            $messages,
            $hasMore,
            optional($messages->first())->id,
        ];
    }

    // Actualizar estado en tiempo real de la cita
    public function sidePanel(\App\Models\Conversation $conversation)
    {
        $authUser = auth()->user();
        $property = $conversation->property;

        $nextVisit = $conversation->visits()
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('visit_date')
            ->first();

        $activeReservation = $conversation->reservations()
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->first();

        return view('chat.partials.side-panel', compact(
            'conversation',
            'property',
            'nextVisit',
            'activeReservation',
            'authUser',
        ));
    }

}
