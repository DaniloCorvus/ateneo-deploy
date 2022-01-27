<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('llamada_id')->nullable()->index();
            $table->bigInteger('funcionario_id')->nullable()->index();
            $table->bigInteger('paciente_id')->nullable()->index();
            $table->bigInteger('tramite_id')->nullable()->index();
            $table->bigInteger('ambito_id')->nullable()->index();
            $table->bigInteger('servicio_id')->nullable()->index();
            $table->bigInteger('tipificacion_id')->nullable()->index();
            $table->timestamps();
            $table->string('fecha', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calls');
    }
}
