<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialTransferTable extends Migration
{
    public function up()
    {
        Schema::create('material_transfer', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('material_id');
		$table->integer('transfer_id');
		$table->integer('quantity');
		$table->integer('material_stock_id')->nullable();
		$table->timestamps();		$table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('material_transfer');
    }
}
