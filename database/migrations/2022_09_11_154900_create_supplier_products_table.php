<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierProductsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_products', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('material_id');
		$table->integer('quantity');
		$table->integer('supplier_id');
		$table->integer('material_in_id');
		$table->integer('total_price')->default('0');
		$table->integer('paid_amount')->default('0');
		$table->integer('due_amount')->default('0');
		$table->timestamps();		$table->string('payment_process')->nullable();
		$table->string('payment_info')->nullable();


        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_products');
    }
}
