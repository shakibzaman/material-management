<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountTable extends Migration
{
    public function up()
    {
        Schema::create('user_account', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('user_id');
		$table->integer('type');
		$table->integer('opening_balance')->default('0');
		$table->integer('total_due')->default('0');
		$table->integer('total_paid')->default('0');
		$table->timestamps();
        $table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('user_account');
    }
}
