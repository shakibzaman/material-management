<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('material_configs', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->integer('type');
            $table->integer('material_price')->default('0');
            $table->integer('knitting_price')->default('0');
            $table->integer('selling_price')->default('0');
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('material_configs');
    }
}
