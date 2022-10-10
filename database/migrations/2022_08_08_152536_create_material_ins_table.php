<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialInsTable extends Migration
{
    public function up()
    {
        Schema::create('material_ins', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('material_id');
		$table->integer('quantity')->nullable();
		$table->integer('unit')->nullable();
		$table->integer('type');
		$table->date('buying_date')->nullable();
		$table->integer('unit_price');
		$table->integer('total_price');
		$table->integer('rest');
		$table->integer('supplier_id');
		$table->string('inv_number');
		$table->integer('purchased_by');
		$table->integer('created_by');
        $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('material_ins');
    }
}
