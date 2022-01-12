<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_ins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Id_Llamada', 100)->index('Id_Llamada');
            $table->unsignedBigInteger('Identificacion_Paciente')->index('Identificacion_Paciente');
            $table->unsignedBigInteger('Identificacion_Agente')->index('Identificacion_Agente');
            $table->unsignedBigInteger('Tipo_Tramite')->nullable()->index('Tipo_Tramite');
            $table->unsignedBigInteger('Tipo_Servicio')->nullable()->index('Tipo_Servicio');
            $table->unsignedBigInteger('Ambito')->nullable()->index('Ambito');
            $table->timestamps();
            $table->enum('status', ['Pendiente', 'Atendida'])->nullable()->default('Pendiente');
            $table->enum('type', ['Presencial', 'CallCenter'])->nullable()->default('CallCenter');
            $table->string('Observation', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_ins');
    }
}
