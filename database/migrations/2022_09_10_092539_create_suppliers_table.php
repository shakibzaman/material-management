<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {

		$table->increments('id');
		$table->string('name');
		$table->string('phone')->nullable();
		$table->string('address')->nullable();
		$table->integer('opening_balance')->default('0');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
