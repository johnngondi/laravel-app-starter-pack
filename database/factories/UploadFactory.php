<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Upload>
 */
class UploadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'organisation_id' => Organisation::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'source_url' => 'uploads/'.fake()->uuid().'.pdf',
            'file_name' => $name.'.pdf',
            'type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => fake()->numberBetween(1024, 5_000_000),
        ];
    }
}
