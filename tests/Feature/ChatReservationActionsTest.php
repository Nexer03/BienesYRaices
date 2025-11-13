<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Property;
use App\Models\SystemCommission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChatReservationActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'null']);
        Notification::fake();
    }

    public function test_client_can_start_reservation_from_chat(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);
        $property = Property::factory()->create(['user_id' => $agent->id, 'listing_type' => 'rent', 'price' => 1500]);
        SystemCommission::create([
            'listing_type' => 'rent',
            'percentage'   => 10,
        ]);

        $conversation = Conversation::create([
            'property_id' => $property->id,
            'agent_id'    => $agent->id,
            'client_id'   => $client->id,
        ]);

        $start = now()->addDays(5)->toDateString();
        $end   = now()->addDays(8)->toDateString();

        $this->actingAs($client)
            ->post(route('chat.reservations.store', $conversation), [
                'start_date' => $start,
                'end_date'   => $end,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('property_reservations', [
            'property_id' => $property->id,
            'user_id'     => $client->id,
            'start_date'  => $start . ' 00:00:00',
            'end_date'    => $end . ' 00:00:00',
            'status'      => 'pending',
            'payment_status' => 'unpaid',
        ]);
    }
}
