<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkContractTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_contract_types', function (Blueprint $table) {
            $table->integer('id', true);
            $table->boolean('conclude')->default(0);
            $table->boolean('modified')->default(0);
            $table->string('name', 50)->nullable();
            $table->string('description', 50)->nullable();
            $table->enum('status', ['Activo', 'Inactivo'])->nullable()->default('Activo');
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
        Schema::dropIfExists('work_contract_types');
    }
}
