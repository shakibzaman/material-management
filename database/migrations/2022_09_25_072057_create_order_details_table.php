<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('order_id');
		$table->integer('product_id');
		$table->integer('color_id')->nullable();
		$table->integer('product_transfer_id');
		$table->integer('selling_price');
		$table->integer('qty');
		$table->integer('line_total');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
