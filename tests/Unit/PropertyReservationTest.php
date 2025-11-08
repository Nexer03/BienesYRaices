<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\PropertyReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PropertyReservationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_detects_overlapping_reservations_for_the_same_property(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $client = User::factory()->create(['role' => 'client']);

        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'listing_type' => 'rent',
            'status' => 'available',
        ]);

        PropertyReservation::create([
            'property_id' => $property->id,
            'user_id' => $client->id,
            'start_date' => '2025-01-10',
            'end_date' => '2025-01-15',
            'total_price' => 1000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->assertTrue(
            PropertyReservation::overlaps($property->id, '2025-01-12', '2025-01-18')
        );

        $this->assertFalse(
            PropertyReservation::overlaps($property->id, '2025-01-16', '2025-01-20')
        );
    }
}
