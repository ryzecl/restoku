<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'category_id' => fake()->numberBetween(1, 2),
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'description' => fake()->text(),
            'img' => fake()->randomElement(
                [
                    'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351',
                    'https://images.unsplash.com/photo-1617421753170-46511a8d73fc',
                    'https://images.unsplash.com/photo-1742633882704-41ec3a57dbb7'
                ]
            ),
            'is_active' => fake()->boolean(),
        ];
    }
}
