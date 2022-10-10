<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockSetsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_sets', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('product_id');
		$table->integer('color_id');
		$table->integer('start_quantity');
		$table->integer('end_quantity');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_sets');
    }
}
