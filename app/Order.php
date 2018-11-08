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
		return $this->belongsToMany(Product::class, 'order_items',
			'orderId', 'productId');
	}

	/**
	 * Save All Order Items for this Client
	 *
	 * @param array $items
	 * @param $userId
	 * @return array
	 */
	public function saveItems(array $items, $orderId)
	{
		$orderId = array_fill(0, count($items), $orderId);

		$orderItem = array_map(function ($itm, $id) {
			return array_merge($itm, ['orderId' => $id]);
		}, $items, $orderId);

		$this->product()->attach($orderItem);

		// Updating original Inventory
		foreach ($items as $prod) {
			$upProduct = $this->product()->find($prod['productId']);
			$upProduct->inventory -= $prod['quantity'];
			$upProduct->save();
		}
		return $this;
	}

}
