<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	/**
	 * Fill all columns [date, clientId]
	 * [closed=0]: Update by [Seller]
	 * @var array
	 */
	protected $guarded = [];

	/**
	 * relationship Order Belongs To Client
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
	public function product()
	{
		return $this->belongsToMany(Product::class);
	}

}
