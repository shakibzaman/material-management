<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTransferTable extends Migration
{
    public function up()
    {
        Schema::create('product_transfer', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('product_id');
		$table->integer('quantity');
		$table->integer('color_id')->nullable();
		$table->integer('rest_quantity');
		$table->integer('transfer_id');
		$table->integer('product_stock_id')->nullable();
		$table->integer('process_fee')->nullable();
		$table->integer('selling_price')->default('0');
		$table->timestamps();		$table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('product_transfer');
    }
}
