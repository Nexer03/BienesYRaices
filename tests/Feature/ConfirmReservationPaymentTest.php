<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmReservationPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_users_can_confirm_a_reservation_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);

        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'listing_type' => 'rent',
            'status' => 'available',
        ]);

        $reservation = PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $client->id,
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-05',
            'total_price' => 2000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)
            ->patchJson(route('reservations.confirmPayment', $reservation), [
                'payment_id' => 'PAY-12345',
                'payment_method' => 'manual',
                'payer_email' => 'client@example.com',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('property_reservations', [
            'id' => $reservation->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_id' => 'PAY-12345',
            'payment_method' => 'manual',
            'payer_email' => 'client@example.com',
        ]);
    }

    /** @test */
    public function non_authorized_users_cannot_confirm_reservations(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $otherAgent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);

        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'listing_type' => 'rent',
            'status' => 'available',
        ]);

        $reservation = PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $client->id,
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-04',
            'total_price' => 1500,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($otherAgent)
            ->patchJson(route('reservations.confirmPayment', $reservation), [
                'payment_id' => 'PAY-9999',
            ])
            ->assertForbidden();

        $reservation->refresh();

        $this->assertSame('pending', $reservation->status);
        $this->assertSame('unpaid', $reservation->payment_status);
        $this->assertNull($reservation->payment_id);
    }
}
