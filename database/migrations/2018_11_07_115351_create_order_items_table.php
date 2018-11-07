<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('order_items', function (Blueprint $table) {
			$table->integer('productId')->unsigned()->index();
			$table->foreign('productId')->references('id')->on('products')->onDelete('cascade');
			$table->integer('orderId')->unsigned()->index();
			$table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade');
			$table->integer('quantity');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('order_items');
	}
}
