<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCie10sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cie10s', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('description', 600);
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            $table->string('code', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cie10s');
    }
}
