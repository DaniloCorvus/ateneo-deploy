<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaitingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('origin', 20);
            $table->string('message_cancell')->nullable();
            $table->integer('type_appointment_id')->nullable();
            $table->integer('sub_type_appointment_id')->nullable();
            $table->integer('profesional_id')->nullable();
            $table->integer('speciality_id')->nullable();
            $table->enum('state', ['Pendiente', 'Cancelado', 'Agendado'])->nullable();
            $table->integer('appointment_id')->nullable();
            $table->timestamp('space_date_assign')->nullable();
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
        Schema::dropIfExists('waiting_lists');
    }
}
