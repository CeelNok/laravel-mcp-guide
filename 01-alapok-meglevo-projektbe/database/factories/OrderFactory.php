<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
	public function definition(): array
	{
		return [
			'customer_id' => Customer::factory(),
			'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
			'total_price' => 0,
			'notes' => fake()->optional(0.3)->sentence(),
		];
	}
}
