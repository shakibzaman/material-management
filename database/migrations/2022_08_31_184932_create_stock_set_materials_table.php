<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockSetMaterialsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_set_materials', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('stock_set_id');
		$table->integer('material_id');
		$table->integer('material_quantity');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_set_materials');
    }
}
