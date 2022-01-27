<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_appointments', function (Blueprint $table) {
            $table->string('description');
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('icon', 50)->nullable();
            $table->tinyInteger('face_to_face')->nullable();
            $table->integer('ips')->nullable();
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
        Schema::dropIfExists('type_appointments');
    }
}
