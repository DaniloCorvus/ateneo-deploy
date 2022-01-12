<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patientsold', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('type_document_id', 11)->nullable();
            $table->string('identifier', 50)->nullable();
            $table->integer('regimen_id')->nullable();
            $table->string('affiliate_type', 50)->nullable();
            $table->string('category_affiliate', 50)->nullable();
            $table->string('secondsurname', 50)->nullable();
            $table->string('surname', 50)->nullable();
            $table->string('firstname', 50)->nullable();
            $table->string('middlename', 50)->nullable();
            $table->string('date_of_birth', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('gener', 20)->nullable();
            $table->string('population_group', 50)->nullable();
            $table->integer('contract_id')->nullable();
            $table->integer('regional_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('eps_id')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address')->nullable();
            $table->string('civil_state', 20)->nullable();
            $table->integer('level_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('municipality_id')->nullable();
            $table->string('email', 50)->nullable();
            $table->string('ips_principal')->nullable();
            $table->enum('zone', ['Urbano', 'Rural'])->nullable();
            $table->text('description')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->string('manager', 50)->nullable();
            $table->string('rol_manager', 50)->nullable();
            $table->string('phone_manager', 50)->nullable();
            $table->string('ethnic_group', 50)->nullable();
            $table->string('token', 11)->nullable();
            $table->string('database', 50)->nullable();
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
        Schema::dropIfExists('patientsold');
    }
}
