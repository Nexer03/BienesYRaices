<?php

namespace Tests\Feature;

use App\Events\MessagesRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'null']);
    }

    public function test_user_can_send_message_with_attachment(): void
    {
        Storage::fake('public');

        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id]);
        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        $file = UploadedFile::fake()->image('evidencia.jpg');

        $response = $this->actingAs($client)
            ->withHeader('Accept', 'application/json')
            ->post(route('chat.send', $conversation), [
                'body' => 'Aquí tienes la foto',
                'attachment' => $file,
            ]);

        $response->assertOk();

        $this->assertDatabaseCount('messages', 1);
        $message = Message::first();
        $this->assertNotNull($message->attachment_path);
        Storage::disk('public')->assertExists($message->attachment_path);
    }

    public function test_marking_messages_as_read_updates_database_and_broadcasts_event(): void
    {
        Event::fake([MessagesRead::class]);

        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id]);
        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $agent->id,
            'body'            => 'Hola',
        ]);

        $this->actingAs($client)
            ->withHeader('Accept', 'application/json')
            ->post(route('chat.read', $conversation), ['message_ids' => [$message->id]])
            ->assertJson(['status' => 'ok']);

        $this->assertNotNull($message->fresh()->read_at);

        Event::assertDispatched(MessagesRead::class, function ($event) use ($conversation, $message, $client) {
            return $event->conversationId === $conversation->id
                && in_array($message->id, $event->messageIds, true)
                && $event->readerId === $client->id;
        });
    }

    public function test_messages_endpoint_supports_pagination_and_search(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id]);
        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        foreach (range(1, 32) as $i) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $i % 2 === 0 ? $agent->id : $client->id,
                'body'            => "Mensaje {$i}",
                'created_at'      => now()->subMinutes(40 - $i),
                'updated_at'      => now()->subMinutes(40 - $i),
            ]);
        }

        $baseUrl = route('chat.messages', $conversation);

        $firstPage = $this->actingAs($agent)
            ->getJson($baseUrl . '?limit=20')
            ->assertOk()
            ->json();

        $this->assertTrue($firstPage['has_more']);
        $this->assertCount(20, $firstPage['messages']);

        $oldest = $firstPage['messages'][0]['id'];

        $secondPage = $this->actingAs($agent)
            ->getJson($baseUrl . '?limit=20&before=' . $oldest)
            ->assertOk()
            ->json();

        $this->assertFalse($secondPage['has_more']);
        $this->assertCount(12, $secondPage['messages']);

        $search = $this->actingAs($agent)
            ->getJson($baseUrl . '?q=Mensaje 3')
            ->assertOk()
            ->json();

        $this->assertNotEmpty($search['messages']);
        collect($search['messages'])->each(function ($message) {
            $this->assertStringContainsString('Mensaje 3', $message['body']);
        });
    }
}
