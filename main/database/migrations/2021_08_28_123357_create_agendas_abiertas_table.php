<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendasAbiertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agendas-abiertas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('regexp_replace')->nullable();
            $table->text('date')->nullable();
            $table->text('action')->nullable();
            $table->json('schedule')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agendas-abiertas');
    }
}
