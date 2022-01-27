<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agendamientos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('type_agenda_id')->nullable()->index();
            $table->integer('type_appointment_id')->nullable()->index();
            $table->integer('ips_id')->nullable()->index();
            $table->integer('eps_id')->nullable()->index();
            $table->integer('location_id')->nullable()->index();
            $table->integer('speciality_id')->nullable()->index();
            $table->integer('person_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('long', 5)->nullable();
            $table->time('hour_start')->nullable();
            $table->time('hour_end')->nullable();
            $table->json('days')->nullable();
            $table->tinyInteger('pending')->nullable()->default(0);
            $table->enum('state', ['Aperturada', 'Cancelada'])->nullable()->default('Aperturada')->index();
            $table->integer('share')->nullable()->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agendamientos');
    }
}
