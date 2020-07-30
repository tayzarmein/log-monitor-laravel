<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGclogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gclogs', function (Blueprint $table) {
            $table->id();
            $table->string("logtype");
            $table->string("server_name");
            $table->dateTime("datetime", 3);
            $table->integer("newgen_before")->nullable();
            $table->integer("newgen_current")->nullable();
            $table->integer("newgen_maximum")->nullable();
            $table->integer("oldgen_before")->nullable();
            $table->integer("oldgen_current")->nullable();
            $table->integer("oldgen_maximum")->nullable();
            $table->integer("heap_before")->nullable();
            $table->integer("heap_current")->nullable();
            $table->integer("heap_maximum")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gclogs');
    }
}
