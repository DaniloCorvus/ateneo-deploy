<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->string('address');
            $table->string('agreements');
            $table->string('category');
            $table->string('city');
            $table->string('code');
            $table->string('country_code');
            $table->string('creation_date');
            $table->boolean('disabled');
            $table->string('epss');
            $table->string('email');
            $table->string('encoding_characters');
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('interface_id');
            $table->string('logo');
            $table->string('name');
            $table->string('parent');
            $table->string('pbx');
            $table->string('regional');
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
        Schema::dropIfExists('institutions');
    }
}
