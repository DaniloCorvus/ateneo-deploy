<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_contracts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('position_id')->nullable()->index();
            $table->integer('company_id')->nullable()->index();
            $table->tinyInteger('liquidated')->nullable()->default(0)->index();
            $table->integer('person_id')->nullable()->index();
            $table->double('salary', 50, 2)->nullable();
            $table->enum('turn_type', ['Rotativo', 'Fijo'])->nullable()->index();
            $table->integer('fixed_turn_id')->nullable();
            $table->date('date_of_admission')->nullable();
            $table->integer('work_contract_type_id')->nullable()->index();
            $table->date('date_end')->nullable()->index();
            $table->timestamps();
            $table->integer('rotating_turn_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_contracts');
    }
}
