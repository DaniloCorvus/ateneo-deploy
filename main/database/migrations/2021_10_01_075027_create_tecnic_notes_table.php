<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTecnicNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('tecnic_notes', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('frequency');
        //     $table->string('alert_percentage');
        //     $table->string('unit_value');
        //     $table->date('date');
        //     $table->string('chance');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('tecnic_notes');
    }
}
