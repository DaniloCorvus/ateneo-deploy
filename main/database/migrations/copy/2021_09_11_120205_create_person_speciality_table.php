<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonSpecialityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_speciality', function (Blueprint $table) {
            $table->bigInteger('person_id')->nullable();
            $table->integer('speciality_id')->nullable();
            $table->timestamps();
            $table->bigInteger('ids', true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_speciality');
    }
}
