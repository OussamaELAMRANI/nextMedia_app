<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	/**
	 * Fill all columns [name, desc, price, inventory]
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * Generate a [Slug] before creating a Product
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();
		static::creating(function ($prod) {
			$prod->slug = uniqid(true);
		});
	}

	/**
	 * Using a [Slug] column for [id] params in URL
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
		return "slug";
	}

	/**
	 * relationship Product Belongs To Client
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo(Client::class, 'clientId');
	}

	/**
	 * Haves a Pivot table Order_items
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function order()
	{
		return $this->belongsToMany(Order::class, 'order_items', 'productId', 'orderId');
	}

}
