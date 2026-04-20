<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
	public function definition(): array
	{
		return [
			'name' => fake()->name(),
			'email' => fake()->unique()->safeEmail(),
			'phone' => fake()->optional(0.75)->phoneNumber(),
			'address' => fake()->optional(0.8)->address(),
		];
	}
}
