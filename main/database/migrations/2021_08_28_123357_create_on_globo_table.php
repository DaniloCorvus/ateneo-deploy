<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnGloboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_globo', function (Blueprint $table) {
            $table->integer('company_id');
            $table->text('company_name');
            $table->integer('location_id');
            $table->text('location_name');
            $table->integer('id', true);
            $table->integer('brand_company')->nullable();
            $table->integer('brand_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('on_globo');
    }
}
