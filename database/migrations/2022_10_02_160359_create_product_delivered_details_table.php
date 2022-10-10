<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDeliveredDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('product_delivered_details', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('product_delivered_id');
		$table->integer('product_id');
		$table->integer('product_stock_id');
		$table->integer('process_fee');
		$table->integer('quantity');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('product_delivered_details');
    }
}
