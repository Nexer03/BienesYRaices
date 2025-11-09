<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisitsReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_visits_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create([
            'role' => 'agent',
            'name' => 'Agente de Pruebas',
        ]);
        $client = User::factory()->create([
            'role' => 'client',
            'name' => 'Cliente Demo',
        ]);
        $property = Property::factory()->create([
            'user_id' => $agent->id,
            'title' => 'Departamento Centro',
            'city' => 'Puerto Vallarta',
        ]);

        Visit::create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'property_id' => $property->id,
            'visit_date' => now()->addDay(),
            'status' => 'confirmed',
            'notes' => 'Visita programada desde el panel',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.visits'));

        $response->assertOk();
        $response->assertSee('Reporte de Visitas', false);
        $response->assertSee('Visitas por estado', false);
        $response->assertSee('Agente de Pruebas', false);
        $response->assertSee('Departamento Centro', false);
    }

    /** @test */
    public function non_admin_users_are_redirected_from_visits_report(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)->get(route('admin.reports.visits'));

        $response->assertRedirect('/');
    }
}
