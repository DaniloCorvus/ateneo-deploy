<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularioResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formulario_responses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            $table->bigInteger('formulario_id')->default(0);
            $table->bigInteger('question_id')->default(0);
            $table->bigInteger('company_id')->default(0);
            $table->bigInteger('sede_id')->default(0);
            $table->text('response');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formulario_responses');
    }
}
