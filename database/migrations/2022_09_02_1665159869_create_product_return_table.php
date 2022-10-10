<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturnTable extends Migration
{
    public function up()
    {
        Schema::create('product_return', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('product_transfer_id')->nullable();
		$table->integer('type')->nullable();
		$table->integer('quantity');
		$table->string('reason',191)->nullable();
		$table->integer('return_by');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('product_return');
    }
}
