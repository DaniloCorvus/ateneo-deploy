<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->default(0);
            $table->string('name')->index('name');
            $table->string('code');
            $table->string('address');
            $table->string('agreements')->nullable();
            $table->string('category');
            $table->string('city');
            $table->integer('globo_id');
            $table->string('country_code');
            $table->string('creation_date');
            $table->boolean('disabled');
            $table->string('email');
            $table->string('encoding_characters');
            $table->bigInteger('interface_id')->default(0);
            $table->text('logo')->nullable();
            $table->string('pbx')->nullable();
            $table->bigInteger('regional_id')->default(0);
            $table->boolean('send_email');
            $table->string('settings');
            $table->string('slogan');
            $table->string('state')->nullable();
            $table->string('telephone');
            $table->string('tin');
            $table->tinyInteger('allow_procedure')->default(0);
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
        Schema::dropIfExists('locations');
    }
}
