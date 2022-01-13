<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('payment_method_id')->index();
            $table->unsignedInteger('bank_id');
            $table->enum('agenda', ['Sala Espera', 'Confirmado']);
            $table->unsignedInteger('contract_id');
            $table->string('price', 9);
            $table->string('reason')->nullable();
            $table->string('observation');
            $table->unsignedInteger('quantity');
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
        Schema::dropIfExists('fees');
    }
}
