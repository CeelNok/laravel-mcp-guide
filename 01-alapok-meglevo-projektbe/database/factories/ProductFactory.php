<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
	public function definition(): array
	{
		$name = fake()->unique()->words(fake()->numberBetween(2, 4), true);

		return [
			'name' => ucfirst($name),
			'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
			'description' => fake()->paragraphs(2, true),
			'price' => fake()->randomFloat(2, 1.99, 499.99),
			'stock' => fake()->numberBetween(0, 200),
			'status' => fake()->randomElement(['active', 'inactive', 'draft']),
		];
	}

	public function active(): static
	{
		return $this->state(['status' => 'active']);
	}

	public function inactive(): static
	{
		return $this->state(['status' => 'inactive']);
	}

	public function draft(): static
{
		return $this->state(['status' => 'draft']);
	}
}
