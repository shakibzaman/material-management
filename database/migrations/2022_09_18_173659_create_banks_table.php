<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration
{
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {

		$table->increments('id');
		$table->string('name');
		$table->string('ac_no');
		$table->integer('limit');
		$table->integer('current_balance');
		$table->decimal('rate',10,0);
		$table->enum('rate_type',['daily','monthly','yearly']);
		$table->timestamps();		$table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('banks');
    }
}
