<?php

namespace Tests\Feature;

use App\Models\AgentApplication;
use App\Models\User;
use App\Notifications\AgentApplicationApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminAgentApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_manage_agent_applications(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $application = AgentApplication::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.agent-applications.index'))
            ->assertRedirect('/');

        $this->actingAs($user)
            ->post(route('admin.agent-applications.approve', $application))
            ->assertRedirect('/');
    }

    public function test_admin_can_approve_an_agent_application(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $applicant = User::factory()->create(['role' => 'client']);
        $application = AgentApplication::factory()->for($applicant, 'user')->create([
            'status' => AgentApplication::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.agent-applications.index'))
            ->post(route('admin.agent-applications.approve', $application))
            ->assertRedirect(route('admin.agent-applications.index'));

        $application->refresh();
        $applicant->refresh();

        $this->assertSame(AgentApplication::STATUS_APPROVED, $application->status);
        $this->assertNull($application->rejection_reason);
        $this->assertSame('agent', $applicant->role);

        Notification::assertSentTo($applicant, AgentApplicationApproved::class);
    }

    public function test_admin_can_reject_an_agent_application(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $applicant = User::factory()->create(['role' => 'client']);
        $application = AgentApplication::factory()->for($applicant, 'user')->create([
            'status' => AgentApplication::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.agent-applications.index'))
            ->post(route('admin.agent-applications.reject', $application), [
                'rejection_reason' => 'Documentación incompleta',
            ])
            ->assertRedirect(route('admin.agent-applications.index'));

        $application->refresh();
        $applicant->refresh();

        $this->assertSame(AgentApplication::STATUS_REJECTED, $application->status);
        $this->assertSame('Documentación incompleta', $application->rejection_reason);
        $this->assertSame('client', $applicant->role);

        Notification::assertNothingSent();
    }
}
