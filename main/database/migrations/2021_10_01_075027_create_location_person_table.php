<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationPersonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_person', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('identifier', 13)->nullable();
            $table->string('nit_company', 11)->nullable();
            $table->string('location', 42)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_person');
    }
}
