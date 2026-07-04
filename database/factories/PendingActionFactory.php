<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\PendingAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PendingAction>
 */
class PendingActionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'actors' => [fake()->numberBetween(1, 100)],
            'notes' => fake()->sentence(),
            'action_type' => fake()->word(),
            'due_at' => fake()->dateTimeBetween('now', '+1 week'),
            'priority' => fake()->randomElement(Priority::cases()),
            'resource_url' => '/'.fake()->slug(),
            'action_button_title' => 'Open Task',
        ];
    }
}
