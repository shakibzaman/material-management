<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('nid')->nullable();
            $table->string('reference')->nullable();
            $table->integer('department_id');
            $table->string('designation')->nullable();
            $table->string('education')->nullable();
            $table->string('salary')->nullable();
            $table->string('status')->default('1');
            $table->dateTime('joining_date')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
