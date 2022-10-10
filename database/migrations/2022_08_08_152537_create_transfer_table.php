<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferTable extends Migration
{
    public function up()
    {
        Schema::create('transfer', function (Blueprint $table) {

		$table->increments('id');
		$table->integer('company_id');
		$table->integer('department_id');
		$table->string('date',199);
		$table->integer('created_by');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer');
    }
}
