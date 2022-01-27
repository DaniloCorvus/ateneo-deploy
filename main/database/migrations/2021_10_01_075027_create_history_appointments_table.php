<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_appointments', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('appointment_id');
            $table->string('description');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->integer('id', true);
            $table->string('icon', 120)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_appointments');
    }
}
