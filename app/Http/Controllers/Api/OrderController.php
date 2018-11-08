<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{


	/**
	 * Check Auth in Route Middleware
	 *
	 * @param Request $request
	 */
	public function store(Request $req)
	{
		//verify request data in OrderRequest
		$req->validate([
			"date" => "required|date",
			"items" => "required|array",
		]);
		$items = $req->get('items');
		//test OrderItem structures, types and values
		$validItems = $this->validateItems($items);

		if (!empty($validItems)) {
			return response()->json($validItems, 400);
		}

		// Validate Product Availability
		$factoryProduct = $this->arrangeQuantities($items);
		$validProducts = $this->isAvailableProduct($factoryProduct);

		if (!empty($validProducts)) {
			return response()->json($validProducts, 400);
		}

		//if all right

		// Get Client id
		$clientId = $req->user()->client()->first()->id;
		$newOrder = new Order([
			'date' => Carbon::parse($req['date'])->format('Y-m-d'),
			'clientId' => $clientId
		]);
		$newOrder->save();
		// Save Items
		$newOrder->saveItems($factoryProduct, $newOrder->id);

		return response()->json([
			"message" => "Successfully Added",
			"Order" => $newOrder->load('product', 'client'),
			"total" => $this->calcTotal($factoryProduct)
		]);

	}

	/**
	 * First Validation Of Items Order
	 * Formatting array
	 * Type of each element
	 * Value of each element > 0
	 *
	 * @param array $items
	 * @return array
	 */
	public function validateItems(array $items)
	{
		$err = [];
		foreach ($items as $item) {
			if (!array_key_exists('productId', $item) || !array_key_exists('quantity', $item)) {
				$err['structure error'] = 'Order Item has invalid format !';
				$err['structure'] = 'You must formatted like this [productId=> value, quantity => value]';
			} else {
				if (!is_integer($item['productId']) || !is_integer($item['quantity'])) {
					$err['Type error'] = "Order Item has invalid type (must be integer) !";
				} else {
					if ($item['productId'] < 0 || $item['quantity'] < 0)
						$err['value error'] = "Order Item has invalid value [productId, quantity] must be great than 0) !";
				}
			}
		}
		return $err;
	}

	/**
	 * Testing Available product By Id and Inventories
	 * Using @function arrangeQuantities() -- Calculate Global Quantities
	 *
	 * @param array $items
	 * @return array
	 */
	public function isAvailableProduct(array $items)
	{
		$err = [];
		foreach ($items as $item) {
			$products = Product::Where('id', $item['productId'])->where('inventory', '>=', $item['quantity'])->first();
			if (!$products)
				$err["Product (${item['productId']})"] = "this product not available !";
		}
		return $err;
	}

	/**
	 * Calculate Quantities for the same Product
	 *
	 * @param array $array
	 * @return array
	 */
	public function arrangeQuantities(array $array)
	{
		$result = array();
		$new = array();

		foreach ($array as $key => $value) {
			$id = $value['productId'];
			$result[$id][] = $value['quantity'];
		}
		foreach ($result as $key => $value) {
			$new[] = array('productId' => $key, 'quantity' => array_sum($value));
		}
		return $new;
	}

	/**
	 * Get Total of this Order
	 *
	 * @param $products
	 * @return float
	 */
	public function calcTotal($products)
	{
		$total = 0;
		foreach ($products as $product) {

			$price = Product::where('id', $product['productId'])->first()->price;
			$total += $price *  $product['quantity'];
		}
		return $total;
	}
}
