<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('logo');
            $table->string('address');
            $table->unsignedBigInteger('agreements_id');
            $table->string('category');
            $table->string('city');
            $table->string('code');
            $table->string('country_code');
            $table->string('creation_date');
            $table->boolean('disabled');
            $table->unsignedBigInteger('epss_id');
            $table->string('email');
            $table->string('encoding_characters');
            $table->unsignedBigInteger('interface_id');
            $table->unsignedBigInteger('parent_id');
            $table->string('pbx');
            $table->unsignedBigInteger('regional_id');
            $table->boolean('send_email');
            $table->string('settings');
            $table->string('slogan');
            $table->string('state');
            $table->string('telephone');
            $table->string('tin');
            $table->unsignedBigInteger('type');
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
        Schema::dropIfExists('administrators');
    }
}
