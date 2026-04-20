<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
	public function run(): void
	{
		$customers = Customer::all();
		$products = Product::all();

		for ($i = 0; $i < 100; $i++) {
			$order = Order::factory()->create([
				'customer_id' => $customers->random()->id,
			]);

			$itemCount = rand(1, 5);
			$totalPrice = 0;

			$usedProducts = $products->random($itemCount);

			foreach ($usedProducts as $product) {
				$quantity = rand(1, 10);
				$unitPrice = $product->price;

				OrderItem::create([
					'order_id' => $order->id,
					'product_id' => $product->id,
					'quantity' => $quantity,
					'unit_price' => $unitPrice,
				]);

				$totalPrice += $quantity * $unitPrice;
			}

			$order->update(['total_price' => $totalPrice]);
		}
	}
}
