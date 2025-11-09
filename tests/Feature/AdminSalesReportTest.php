<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSalesReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_sales_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create([
            'role' => 'agent',
            'name' => 'Agente Ventas',
        ]);

        $soldProperty = Property::factory()->create([
            'user_id' => $agent->id,
            'status' => 'sold',
            'price' => 1250000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.sales'));

        $response->assertOk();
        $response->assertSeeText('Agente Ventas');
        $response->assertSeeText('Total de propiedades vendidas');
        $response->assertSeeText('Valor total vendido');
        $response->assertSee('$' . number_format($soldProperty->price, 2, '.', ','));
    }
}
