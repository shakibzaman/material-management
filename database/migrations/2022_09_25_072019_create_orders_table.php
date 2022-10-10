<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {

		$table->increments('id');
		$table->string('invoice_id');
		$table->integer('department_id');
		$table->integer('customer_id');
		$table->integer('total');
		$table->integer('sub_total');
		$table->integer('discount');
		$table->integer('paid');
		$table->integer('due');
		$table->date('date');
		$table->string('payment_process',191);
		$table->string('payment_info',191);
		$table->integer('created_by');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
