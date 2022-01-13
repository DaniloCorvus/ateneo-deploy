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
            $table->bigInteger('call_id')->default(0)->index('call_id');
            $table->bigInteger('space_id')->nullable()->index('space_id');
            $table->bigInteger('location_id')->nullable()->index('location_id');
            $table->bigInteger('company_id')->nullable()->index('company_id');
            $table->enum('state', ['Agendado', 'Cancelado', 'Asistio', 'Pendiente', 'SalaEspera', 'Confirmado', 'NoAsistio'])->nullable()->default('Agendado');
            $table->string('diagnostico', 200)->nullable();
            $table->integer('professional_id')->nullable()->index('professional_id');
            $table->integer('ips_id')->nullable();
            $table->integer('speciality_id')->nullable()->index('speciality_id');
            $table->string('speciality', 250)->nullable();
            $table->string('procedure', 250)->nullable();
            $table->string('date', 50)->nullable();
            $table->string('origin', 50)->nullable();
            $table->integer('procedure_id')->nullable()->index('procedure_id');
            $table->string('price', 50)->nullable();
            $table->longText('observation')->nullable();
            $table->string('reason_cancellation')->nullable();
            $table->dateTime('cancellation_at')->nullable();
            $table->string('ips')->nullable();
            $table->string('code', 50)->nullable();
            $table->string('link', 50)->nullable();
            $table->tinyInteger('on_globo')->nullable();
            $table->string('profesional', 100)->nullable();
            $table->bigInteger('globo_id')->nullable();
            $table->json('globo_response')->nullable();
            $table->integer('mmedical')->nullable();
            $table->string('message_confirm', 55)->nullable();
            $table->integer('payed')->nullable()->default(0);
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
        Schema::dropIfExists('appointments');
    }
}
