<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {

		$table->increments('id');
		$table->string('name');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('units');
    }
}
