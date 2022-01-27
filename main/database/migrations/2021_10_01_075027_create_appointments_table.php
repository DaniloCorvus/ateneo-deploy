<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('call_id')->default(0);
            $table->bigInteger('space_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->enum('state', ['Agendado', 'Cancelado', 'Asistio', 'Pendiente', 'SalaEspera', 'Confirmado', 'NoAsistio'])->nullable()->default('Agendado');
            $table->string('diagnostico', 200)->nullable();
            $table->string('professional_id', 250)->nullable();
            $table->string('ips_id', 250)->nullable();
            $table->string('speciality_id', 250)->nullable();
            $table->string('speciality', 250)->nullable();
            $table->string('procedure', 250)->nullable();
            $table->string('date', 50)->nullable();
            $table->string('origin', 50)->nullable();
            $table->string('procedure_id', 50)->nullable();
            $table->string('price', 50)->nullable();
            $table->longText('observation')->nullable();
            $table->string('reason_cancellation')->nullable();
            $table->string('message_confirm')->nullable();
            $table->dateTime('cancellation_at')->nullable();
            $table->timestamps();
            $table->string('ips')->nullable();
            $table->string('code', 50)->nullable();
            $table->string('link', 50)->nullable();
            $table->tinyInteger('on_globo')->nullable();
            $table->string('profesional')->nullable();
            $table->bigInteger('globo_id')->nullable();
            $table->json('globo_response')->nullable();
            $table->integer('mmedical')->nullable();
            $table->integer('payed')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
