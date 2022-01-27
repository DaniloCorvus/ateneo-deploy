<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number');
            $table->integer('administrator_id')->nullable();
            $table->string('name');
            $table->string('company_id', 100)->nullable();
            $table->string('department_id', 100)->nullable();
            $table->string('location_id', 100)->nullable();
            $table->string('regimen_id', 100)->nullable();
            $table->string('eps_id', 100)->nullable();
            $table->string('site');
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
        Schema::dropIfExists('contracts');
    }
}
