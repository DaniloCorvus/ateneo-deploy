<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotatingTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rotating_turns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->boolean('extra_hours');
            $table->integer('entry_tolerance');
            $table->integer('exit_tolerance');
            $table->string('color', 191);
            $table->timestamps();
            $table->integer('empresa_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotating_turns');
    }
}
