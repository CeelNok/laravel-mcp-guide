<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'slug',
		'description',
		'price',
		'stock',
		'status',
	];

	protected $casts = [
		'price' => 'decimal:2',
		'stock' => 'integer',
	];

	/**
	 * Get all order items that include this product.
	 */
	public function orderItems(): HasMany
	{
		return $this->hasMany(OrderItem::class);
	}
}
