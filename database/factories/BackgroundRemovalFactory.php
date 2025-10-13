<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BackgroundRemoval>
 */
class BackgroundRemovalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->word().'.jpg';

        return [
            'user_id' => User::factory(),
            'original_filename' => $filename,
            'original_path' => 'background-removals/originals/'.$filename,
            'processed_path' => 'background-removals/processed/'.$filename,
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB to 5MB
            'replicate_prediction_id' => $this->faker->uuid(),
            'processing_cost' => $this->faker->randomFloat(4, 0.0100, 0.0200), // $0.01-0.02
            'processed_at' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_path' => null,
            'processed_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_path' => null,
            'processed_at' => null,
            'processing_cost' => 0,
        ]);
    }
}
