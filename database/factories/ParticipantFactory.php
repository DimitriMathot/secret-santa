<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'token' => fake()->sha256(),
            'has_viewed_assignment' => false,
            'viewed_at' => null,
        ];
    }
}
