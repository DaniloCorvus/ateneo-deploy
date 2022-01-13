<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendamientoCupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agendamiento_cup', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('agendamiento_id')->nullable()->index('agendamiento_id');
            $table->integer('cup_id')->nullable()->index('cup_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('update_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agendamiento_cup');
    }
}
