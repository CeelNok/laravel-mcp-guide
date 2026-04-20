<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
	public function run(): void
	{
		// 8 active, 6 inactive, 6 draft = 20 total
		Product::factory()->count(8)->active()->create();
		Product::factory()->count(6)->inactive()->create();
		Product::factory()->count(6)->draft()->create();
	}
}
