<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {

		$table->increments('id');
		$table->text('name');
		$table->string('phone')->nullable();
		$table->text('address');
		$table->string('nid')->nullable();
		$table->string('reference')->nullable();
		$table->integer('department_id');
		$table->string('designation');
		$table->string('education');
		$table->string('salary');
		$table->date('joining_date');
		$table->string('status',190)->default('1');
        $table->timestamps();
		$table->integer('created_by');


        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
