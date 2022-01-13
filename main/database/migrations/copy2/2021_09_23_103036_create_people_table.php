<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier', 20)->nullable()->unique('funcionario_identidad_unique');
            $table->integer('type_document_id')->nullable()->index('type_document_id');
            $table->string('first_name', 191);
            $table->string('second_name', 191)->nullable();
            $table->string('first_surname', 191)->nullable();
            $table->string('second_surname', 191)->nullable();
            $table->string('full_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('birth_place')->nullable();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('optional_phone', 15)->nullable();
            $table->string('cellphone', 191)->nullable();
            $table->string('email', 191);
            $table->text('address')->nullable();
            $table->enum('marital_status', ['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)', 'Union Libre'])->nullable();
            $table->string('degree_instruction', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->string('talla_pantalon', 191)->nullable();
            $table->string('talla_bata', 191)->nullable();
            $table->string('talla_botas', 191)->nullable();
            $table->string('talla_camisa', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->string('image_blob', 50)->nullable();
            $table->integer('eps_id')->nullable();
            $table->integer('compensation_fund_id')->nullable();
            $table->string('degree', 191)->nullable();
            $table->integer('number_of_children')->default(0);
            $table->integer('people_type_id')->nullable();
            $table->integer('severance_fund_id')->nullable();
            $table->integer('shirt_size')->nullable();
            $table->integer('pension_fund_id')->nullable();
            $table->string('personId', 191)->nullable();
            $table->string('persistedFaceId', 191)->nullable();
            $table->enum('sex', ['Femenino', 'Masculino'])->nullable();
            $table->enum('status', ['Activo', 'Inactivo', 'Liquidado'])->default('Activo');
            $table->integer('pants_size')->default(0);
            $table->string('signature')->nullable();
            $table->string('color', 20)->nullable();
            $table->string('medical_record', 20)->nullable();
            $table->string('company_id')->nullable();
            $table->integer('specialities')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('municipality_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('date_last_session', 20)->nullable();
            $table->timestamps();
            $table->tinyInteger('to_globo')->nullable()->default(0);
            $table->string('cell_phone', 12)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
