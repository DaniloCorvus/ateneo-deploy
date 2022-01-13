<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixedTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_turns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->boolean('extra_hours');
            $table->integer('entry_tolerance');
            $table->integer('leave_tolerance');
            $table->string('color', 191);
            $table->timestamps();
            $table->enum('state', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_turns');
    }
}
