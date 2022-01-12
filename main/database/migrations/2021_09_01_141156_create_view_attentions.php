<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewAttentions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW attentions AS select `appointments`.`code` as `consecutivo`, `type_documents`.`code` as `tipo_documnto`, Concat_ws(\" \",patients.firstname, patients.surname) As nombre, `patients`.`date_of_birth` as `cumple`, `patients`.`gener` as `sexo`, `patients`.`phone` as `telefono`, `patients`.`address` as `direccion`, `municipalities`.`name` as `municipio`, `departments`.`name` as `departamento`, `administrators`.`name` as `eps`, `regimen_types`.`name` as `regimen`, `locations`.`name` as `lugar`, `spaces`.`hour_start` as `fecha_cita`, Concat_ws(\" \",agente.first_name, agente.first_surname) As asigna, `appointments`.`state` as `estado`, Concat_ws(\" \",doctor.first_name, doctor.first_surname) As doctor, `type_appointments`.`name` as `consulta`, `specialities`.`name` as `especialidad`, `cups`.`code` as `cup`, `cups`.`description` as `cup_name`, `cie10s`.`description` as `diagnostico`, `appointments`.`ips` as `ips_remisora`, `appointments`.`profesional` as `professional_remisor`, `appointments`.`speciality` as `speciality_remisor` from `agendamientos` inner join `spaces` on `agendamientos`.`id` = `spaces`.`agendamiento_id` inner join `appointments` on `spaces`.`id` = `appointments`.`space_id` inner join `call_ins` on `call_ins`.`id` = `appointments`.`call_id` inner join `patients` on `patients`.`identifier` = `call_ins`.`Identificacion_Paciente` inner join `type_documents` on `type_documents`.`id` = `patients`.`type_document_id` inner join `municipalities` on `municipalities`.`id` = `patients`.`municipality_id` inner join `departments` on `departments`.`id` = `patients`.`department_id` inner join `administrators` on `administrators`.`id` = `patients`.`eps_id` inner join `regimen_types` on `regimen_types`.`id` = `patients`.`regimen_id` inner join `locations` on `locations`.`id` = `agendamientos`.`location_id` inner join `people` as `agente` on `agente`.`identifier` = `call_ins`.`Identificacion_Agente` inner join `people` as `doctor` on `doctor`.`id` = `agendamientos`.`person_id` inner join `type_appointments` on `type_appointments`.`id` = `agendamientos`.`type_agenda_id` inner join `specialities` on `specialities`.`id` = `agendamientos`.`speciality_id` inner join `cups` on `cups`.`id` = `appointments`.`procedure` inner join `cie10s` on `cie10s`.`id` = `appointments`.`diagnostico`");
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW attentions");
    }
}
