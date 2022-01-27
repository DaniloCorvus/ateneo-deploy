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
            $table->integer('type_appointment_id')->nullable()->index();
            $table->integer('sub_type_appointment_id')->nullable()->index();
            $table->integer('profesional_id')->nullable()->index();
            $table->integer('speciality_id')->nullable()->index();
            $table->enum('state', ['Pendiente', 'Cancelado', 'Agendado'])->nullable()->index();
            $table->integer('appointment_id')->nullable()->index();
            $table->timestamp('space_date_assign')->nullable();
            $table->string('message_cancell', 55)->nullable();
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
