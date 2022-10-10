<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomesTable extends Migration
{
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {

		$table->increments('id');;
		$table->date('entry_date')->nullable();
		$table->decimal('amount',15,2)->nullable();
		$table->string('description')->nullable();
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->integer('income_category_id')->unsigned()->nullable();
		$table->integer('created_by_id')->unsigned()->nullable();
		$table->integer('releted_id')->nullable();
		$table->integer('releted_id_type')->nullable();
		$table->integer('department_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
