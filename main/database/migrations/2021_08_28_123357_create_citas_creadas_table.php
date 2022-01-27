<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitasCreadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citas-creadas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code', 16)->nullable();
            $table->string('call_identificacion_paciente', 15)->nullable();
            $table->string('call_identificacion_agente', 10)->nullable();
            $table->integer('call_tipo_tramite')->nullable();
            $table->integer('call_tipo_servicio')->nullable();
            $table->integer('call_ambito')->nullable();
            $table->string('space_hour_start', 19)->nullable();
            $table->integer('space_person_id')->nullable();
            $table->string('space_classname', 11)->nullable();
            $table->string('space_hour_end', 19)->nullable();
            $table->integer('space_long')->nullable();
            $table->string('location_id', 36)->nullable();
            $table->string('company_id', 11)->nullable();
            $table->string('state', 1)->nullable();
            $table->string('remision_diagnostico', 4)->nullable();
            $table->string('remision_speciality', 32)->nullable();
            $table->string('remision_doctor', 55)->nullable();
            $table->string('remision_ips', 63)->nullable();
            $table->string('remision_date', 10)->nullable();
            $table->integer('remision_procedure')->nullable();
            $table->string('origin', 7)->nullable();
            $table->integer('price')->nullable();
            $table->string('observation', 1313)->nullable();
            $table->string('reason_cancellation', 238)->nullable();
            $table->string('cancellation_at', 26)->nullable();
            $table->string('created_at', 23)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citas-creadas');
    }
}
