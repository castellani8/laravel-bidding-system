<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => fake()->name,
            'description' => fake()->text,
            'images'      => [fake()->imageUrl, fake()->imageUrl, fake()->imageUrl],
            'start_price' => fake()->randomFloat(2, 1, 1000),
            'status'      => fake()->randomElement(['ACTIVE', 'INACTIVE', 'FINISHED']),
            'starts_at'   => fake()->dateTimeBetween('now', '+2 minutes'),
            'ends_at'     => fake()->dateTimeBetween('now', '+2 weeks'),
            'created_by'  => User::factory()->create()
        ];
    }
}
