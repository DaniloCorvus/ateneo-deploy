<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index('name');
            $table->string('tin');
            $table->integer('dv')->nullable();
            $table->string('address');
            $table->string('code');
            $table->string('agreements')->nullable();
            $table->string('category');
            $table->string('city');
            $table->string('country_code');
            $table->string('creation_date');
            $table->boolean('disabled');
            $table->string('email');
            $table->string('encoding_characters');
            $table->bigInteger('interface_id')->default(0);
            $table->text('logo')->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->string('pbx')->nullable();
            $table->bigInteger('regional_id')->default(0);
            $table->boolean('send_email');
            $table->string('settings');
            $table->string('slogan');
            $table->string('state')->nullable();
            $table->string('telephone');
            $table->unsignedBigInteger('type');
            $table->string('api_key', 100)->nullable();
            $table->timestamps();
            $table->string('simbol', 10)->nullable();
            $table->integer('globo_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
