<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('agendamiento_id')->nullable()->index('agendamiento_id');
            $table->dateTime('hour_start')->nullable()->index();
            $table->bigInteger('person_id')->nullable()->index('person_id');
            $table->string('className', 50)->nullable();
            $table->dateTime('hour_end')->nullable()->index();
            $table->integer('long')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('backgroundColor', 50)->nullable();
            $table->enum('state', ['Activo', 'Cancelado'])->default('Activo');
            $table->integer('share')->nullable()->default(1);
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
        Schema::dropIfExists('spaces');
    }
}
