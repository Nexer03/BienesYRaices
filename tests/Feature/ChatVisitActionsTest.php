<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChatVisitActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'null']);
        Notification::fake();
    }

    public function test_agent_can_create_visit_from_chat(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id, 'listing_type' => 'sale']);
        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        $this->actingAs($agent)
            ->post(route('chat.visits.store', $conversation), [
                'visit_date' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d\TH:i'),
                'notes'      => 'Llevar contrato',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('visits', [
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
            'status'      => 'pending',
            'notes'       => 'Llevar contrato',
        ]);
    }

    public function test_client_can_confirm_visit(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id, 'listing_type' => 'sale']);
        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        $visit = Visit::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
            'visit_date'  => now()->addDays(3),
            'status'      => 'pending',
        ]);

        $this->actingAs($client)
            ->patch(route('chat.visits.confirm', [$conversation, $visit]))
            ->assertRedirect();

        $this->assertDatabaseHas('visits', [
            'id'     => $visit->id,
            'status' => 'confirmed',
        ]);
    }
}
