<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacienteUltimoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paciente-ultimo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('scEmpresa', 57)->nullable();
            $table->string('Desc_Sede', 32)->nullable();
            $table->string('Id_Tipodocumento', 4)->nullable();
            $table->bigInteger('DocumentoPaciente')->nullable();
            $table->string('PrimerApellidoPaciente', 40)->nullable();
            $table->string('SegundoApellidoPaciente', 47)->nullable();
            $table->string('PrimerNombrePaciente', 34)->nullable();
            $table->string('SegundoNombrePaciente', 30)->nullable();
            $table->string('FechaNacimientoPaciente', 10)->nullable();
            $table->string('GeneroPaciente', 1)->nullable();
            $table->string('Desc_grupopoblacion', 7)->nullable();
            $table->string('Desc_ocupacion', 145)->nullable();
            $table->string('Desc_niveleducativo', 47)->nullable();
            $table->string('MailPaciente', 121)->nullable();
            $table->string('TelefonoPaciente', 100)->nullable();
            $table->string('CarnetPaciente', 20)->nullable();
            $table->string('ReligionPaciente', 20)->nullable();
            $table->string('ZonaPaciente', 6)->nullable();
            $table->string('DireccionPaciente', 100)->nullable();
            $table->string('ObservacionPaciente', 242)->nullable();
            $table->string('GrupoSanguineoPaciente', 10)->nullable();
            $table->string('Administradora', 86)->nullable();
            $table->string('CodigoDepartamento', 2)->nullable();
            $table->string('DescDepartamento', 18)->nullable();
            $table->string('CodigoCiudad', 3)->nullable();
            $table->string('DescCiudad', 54)->nullable();
            $table->string('Municipio', 10)->nullable()->index('Municipio');
            $table->string('EstadoPaciente', 10)->nullable();
            $table->string('DescRegimen', 33)->nullable();
            $table->string('ResponsablePaciente', 82)->nullable();
            $table->string('ResponsableRol', 51)->nullable();
            $table->string('ResponsableTelefono', 34)->nullable();
            $table->string('EstadoCivilPaciente', 12)->nullable();
            $table->string('Desc_Grupoetnico', 58)->nullable();
            $table->index(['CodigoDepartamento', 'DescDepartamento', 'CodigoCiudad', 'DescCiudad'], 'CodigoDepartamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paciente-ultimo');
    }
}
