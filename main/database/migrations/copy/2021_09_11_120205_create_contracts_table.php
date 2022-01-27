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
            $table->string('name');
            $table->string('code');
            $table->string('number');
            $table->unsignedInteger('administrator_id')->index();
            $table->integer('company_id')->nullable()->index();
            $table->enum('contract_type', ['Capita', 'Evento', 'Particular']);
            $table->integer('eps_id')->nullable()->index();
            $table->string('site', 50)->nullable();
            $table->integer('regimen_id')->nullable()->index();
            $table->integer('location_id')->nullable()->index();
            $table->integer('department_id')->nullable()->index();
            $table->unsignedInteger('payment_method_id')->index();
            $table->unsignedInteger('benefits_plan_id')->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('policy');
            $table->string('price');
            $table->unsignedInteger('price_list_id');
            $table->string('variation');
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
