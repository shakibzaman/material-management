<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDeliveredTable extends Migration
{
    public function up()
    {
        Schema::create('product_delivered', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('product_id');
		$table->integer('quantity');
		$table->integer('company_id')->nullable();
		$table->integer('process_fee')->nullable();
		$table->integer('bill_fee')->nullable();
		$table->integer('sub_total');
		$table->integer('total');
		$table->integer('paid')->nullable();
		$table->integer('due')->nullable();
		$table->integer('discount')->nullable();
		$table->date('date')->nullable();
		$table->timestamps();
        $table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('product_delivered');
    }
}
