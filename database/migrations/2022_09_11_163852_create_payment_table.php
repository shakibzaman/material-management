<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('amount');
		$table->string('payment_process');
		$table->string('payment_info');
		$table->integer('user_account_id');
		$table->integer('releted_id');
		$table->integer('releted_id_type');
		$table->timestamps();
        $table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
