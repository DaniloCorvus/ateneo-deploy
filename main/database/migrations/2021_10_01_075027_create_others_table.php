<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOthersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('others', function (Blueprint $table) {
            $table->string('address');
            $table->string('agreements')->nullable();
            $table->string('category');
            $table->string('city');
            $table->string('code');
            $table->string('country_code');
            $table->string('creation_date');
            $table->boolean('disabled');
            $table->string('email');
            $table->string('encoding_characters');
            $table->unsignedBigInteger('id');
            $table->bigInteger('interface_id')->default(0);
            $table->text('logo')->nullable();
            $table->string('name');
            $table->bigInteger('parent_id')->default(0);
            $table->string('pbx')->nullable();
            $table->bigInteger('regional_id')->default(0);
            $table->boolean('send_email');
            $table->string('settings');
            $table->string('slogan');
            $table->string('state')->nullable();
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
        Schema::dropIfExists('others');
    }
}
