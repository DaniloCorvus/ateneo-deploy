<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('number')->nullable();
            $table->string('code', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->integer('cuote')->nullable();
            $table->integer('regimen_id')->nullable();
            $table->integer('type')->nullable();
            $table->integer('cuote_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('levels');
    }
}
