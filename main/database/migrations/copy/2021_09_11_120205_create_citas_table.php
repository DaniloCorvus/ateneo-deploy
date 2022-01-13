<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('code');
            $table->text('call_identificacion_paciente')->nullable();
            $table->text('call_identificacion_agente')->nullable();
            $table->text('call_tipo_tramite')->nullable();
            $table->text('call_tipo_servicio')->nullable();
            $table->text('call_ambito')->nullable();
            $table->text('space_hour_start')->nullable();
            $table->text('space_person_id')->nullable();
            $table->text('space_classname')->nullable();
            $table->text('space_hour_start_2')->nullable();
            $table->text('space_long')->nullable();
            $table->text('location_id')->nullable();
            $table->text('company_id')->nullable();
            $table->text('state')->nullable();
            $table->text('remision_diagnostico')->nullable();
            $table->text('remision_speciality')->nullable();
            $table->text('remision_doctor')->nullable();
            $table->text('remision_ips')->nullable();
            $table->text('remision_date')->nullable();
            $table->text('remision_procedure')->nullable();
            $table->text('origin')->nullable();
            $table->text('price')->nullable();
            $table->text('observation')->nullable();
            $table->text('reason_cancellation')->nullable();
            $table->text('cancellation_at')->nullable();
            $table->text('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citas');
    }
}
