<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('expense_categories', function (Blueprint $table) {

		$table->increments('id');;
		$table->string('name')->nullable();
		$table->timestamp('created_at');
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->integer('created_by_id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_categories');
    }
}
