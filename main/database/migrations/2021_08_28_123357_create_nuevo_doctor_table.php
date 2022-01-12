<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNuevoDoctorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nuevo_doctor', function (Blueprint $table) {
            $table->string('identifier', 12)->nullable();
            $table->string('type_document_id', 16)->nullable();
            $table->string('external_id', 11)->nullable();
            $table->string('first_name', 13)->nullable();
            $table->string('second_name', 14)->nullable();
            $table->string('first_surname', 13)->nullable();
            $table->string('second_surname', 14)->nullable();
            $table->string('birth_date', 10)->nullable();
            $table->string('birth_place', 65)->nullable();
            $table->string('phone', 14)->nullable();
            $table->string('cellphone', 14)->nullable();
            $table->string('email', 43)->nullable();
            $table->string('address', 82)->nullable();
            $table->string('municipality_id', 32)->nullable();
            $table->string('department_id', 34)->nullable();
            $table->string('marital_status', 14)->nullable();
            $table->string('image', 165)->nullable();
            $table->string('people_type_id', 14)->nullable();
            $table->string('sex', 3)->nullable();
            $table->string('status', 8)->nullable();
            $table->string('signature', 210)->nullable();
            $table->string('medical_record', 14)->nullable();
            $table->string('specialities', 63)->nullable();
            $table->string('date_last_session', 29)->nullable();
            $table->integer('id', true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nuevo_doctor');
    }
}
