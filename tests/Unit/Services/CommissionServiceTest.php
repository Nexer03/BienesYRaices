<?php

namespace Tests\Unit\Services;

use App\Models\SystemCommission;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_agent_and_customer_rates_for_specific_agent(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        SystemCommission::create([
            'user_id' => null,
            'listing_type' => 'both',
            'percentage' => 4.5,
            'customer_percentage' => 8.5,
            'effective_from' => now()->subMonths(1),
        ]);

        SystemCommission::create([
            'user_id' => $agent->id,
            'listing_type' => 'sale',
            'percentage' => 6.0,
            'customer_percentage' => 10.0,
            'effective_from' => now()->subWeek(),
        ]);

        $service = new CommissionService();

        $this->assertSame(6.0, $service->rateFor($agent->id, 'sale'));
        $this->assertSame(10.0, $service->customerRateFor($agent->id, 'sale'));
        $this->assertSame(600.0, $service->calculate($agent->id, 'sale', 10_000));
        $this->assertSame(1000.0, $service->calculateCustomer($agent->id, 'sale', 10_000));
    }

    /** @test */
    public function it_falls_back_to_general_commission_when_agent_has_no_specific_rule(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        SystemCommission::create([
            'user_id' => null,
            'listing_type' => 'rent',
            'percentage' => 3.75,
            'customer_percentage' => 6.5,
        ]);

        $service = new CommissionService();

        $this->assertSame(3.75, $service->rateFor($agent->id, 'rent'));
        $this->assertSame(6.5, $service->customerRateFor($agent->id, 'rent'));
        $this->assertSame(37.5, $service->calculate($agent->id, 'rent', 1_000));
        $this->assertSame(65.0, $service->calculateCustomer($agent->id, 'rent', 1_000));
    }
}
