<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {

		$table->increments('id');;
		$table->date('entry_date')->nullable();
		$table->decimal('amount',15,2)->nullable();
		$table->string('description')->nullable();
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->integer('transfer_id')->nullable();
		$table->integer('expense_category_id')->unsigned()->nullable();
		$table->integer('created_by_id')->unsigned()->nullable();
		$table->integer('department_id')->nullable();
		$table->integer('material_id')->nullable();
		$table->integer('transfer_product_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
