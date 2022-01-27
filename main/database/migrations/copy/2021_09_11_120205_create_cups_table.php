<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cups', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('type')->default(0);
            $table->string('code', 20)->nullable();
            $table->string('description')->nullable()->index();
            $table->text('recomendation')->nullable();
            $table->string('speciality', 50)->nullable();
            $table->string('nickname')->nullable();
            $table->timestamps();
            $table->integer('is_procedure')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cups');
    }
}
