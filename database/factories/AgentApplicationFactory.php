<?php

namespace Database\Factories;

use App\Models\AgentApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AgentApplication>
 */
class AgentApplicationFactory extends Factory
{
    protected $model = AgentApplication::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'rfc' => strtoupper($this->faker->bothify('???######???')),
            'curp' => strtoupper($this->faker->bothify('????######H?????##')),
            'status' => AgentApplication::STATUS_PENDING,
            'rejection_reason' => null,
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => [
            'status' => AgentApplication::STATUS_APPROVED,
        ]);
    }

    public function rejected(?string $reason = null): self
    {
        return $this->state(fn () => [
            'status' => AgentApplication::STATUS_REJECTED,
            'rejection_reason' => $reason ?? $this->faker->sentence(),
        ]);
    }
}
