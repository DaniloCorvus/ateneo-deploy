<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegimeTecnicNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regime_tecnic_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('regime_id');
            $table->unsignedInteger('tecnic_note_id');
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
        Schema::dropIfExists('regime_tecnic_notes');
    }
}
