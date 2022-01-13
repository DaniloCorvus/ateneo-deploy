<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubTypeAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_type_appointments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('type_appointment_id');
            $table->string('description');
            $table->string('name');
            $table->tinyInteger('company_owner')->nullable()->default(0);
            $table->tinyInteger('procedure')->nullable()->default(0);
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
        Schema::dropIfExists('sub_type_appointments');
    }
}
