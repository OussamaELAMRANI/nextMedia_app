<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
	/**
	 * @param $slug
	 * @return Product
	 */
	public function show($slug)
	{
		return Product::where('slug', $slug)->where('inventory', '>', '0')->first();
	}

	/**
	 * get All products have inventory > 0
	 *
	 * @return [Product]
	 */
	public function index()
	{
		return Product::where('inventory', '>', '0')->get();
	}

	public function getSellerProduct($slug, Request $req)
	{
		$clientId = $req->user()->client()->first()->id;
		return Product::where('slug', $slug)->where('clientId', $clientId)->first();
	}

	/**
	 * @param Request $req
	 */
	public function getSellerProducts(Request $req)
	{
		$clientId = $req->user()->client()->first()->id;
		$myProducts = Product::where('clientId', $clientId)->get();
		if ($myProducts)
			return response()->json($myProducts, 200);

		return $this->notFoundResponse();
	}

	/**
	 * Check Auth User [on route] and his type
	 * Update Just a part come from the request [name || Desc || inventory || price]
	 *
	 * @param $slug
	 * @param Request $request
	 * @return mixed
	 */
	public function update($slug, Request $req)
	{
		// Detect if this product is exist and of this Client
		$clientId = $req->user()->client()->first()->id;
		$upProduct = Product::where('slug', $slug)->where('clientId', $clientId)->first();

		if ($upProduct) {
			$upProduct->name = ($req['name']) ?? $upProduct->name;
			$upProduct->description = ($req['description']) ?? $upProduct->description;
			$upProduct->inventory = ($req['inventory']) ?? $upProduct->inventory;
			$upProduct->price = ($req['price']) ?? $upProduct->price;
			$upProduct->save();
			return response()->json(['message' => 'successfully update this product'], 201);
		}
		return $this->notFoundResponse();
	}

	/**
	 * Check Auth User [on route] and his type
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		// Verify data
		$request->validate([
			'name' => 'required|string',
			'description' => 'required|string',
			'price' => 'required|numeric|min:0',
			'inventory' => 'required|integer|min:1',
		]);

		$clientId = $request->user()->client()->first()->id;
		//add if not error detected
		$newProdct = new Product([
			'name' => $request->name,
			'description' => $request->description,
			'price' => $request->price,
			'inventory' => $request->inventory,
			'clientId' => $clientId
		]);
		$newProdct->save();

		return response()->json(['message' => 'Successfully add']);
	}

	/**
	 * Has Middleware in routes
	 *
	 * @param $slug
	 * @return String message
	 */
	public function delete($slug)
	{
		$product = Product::where('slug', $slug)->first();

		if ($product) {
			Product::destroy($product->id);
			return response()->json(['message' => 'Successfully deleted']);
		}

		return $this->notFoundResponse();
	}

	/**
	 * The Seller Cans close the Order
	 *
	 * @param $orderId
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function closeOrder($orderId)
	{
		$order = Order::find($orderId);

		if ($order && $order->where('closed', '==', 0)->first()) {
			$order->closed = 1;
			$order->save();
			return response()->json(['message' => 'Successfully closed !', 'Order' => $order]);
		}

		return response()->json(['message' => 'Order not found or already closed !']);
	}

	/**
	 * Not Found Method with 404 status
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function notFoundResponse()
	{
		return response()->json(['message' => 'this product not found'], 404);
	}
}
