<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('income_categories', function (Blueprint $table) {

		$table->increments('id');
		$table->string('name')->nullable();
		$table->timestamp('created_at')->nullable();
		$table->timestamp('updated_at')->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->integer('created_by_id')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('income_categories');
    }
}
