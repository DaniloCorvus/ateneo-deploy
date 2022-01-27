<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAppointmentPendingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    private function createView(): string
    {
        return 'CREATE VIEW appointment_pendings AS
               select `appointments`.`id`, `eps`.`name` as `eps`, 
               DATE_FORMAT(spaces.hour_start, "%Y-%m-%d %h:%i %p") As date, 
               Concat_ws(" ", people.first_name, people.first_surname) As professional, 
               Concat_ws(" ", patients.firstname,  patients.surname, patients.identifier) As patient, 
               `appointments`.`price` as `copago`, `appointments`.`observation` as `description`,
                `appointments`.`state`, `appointments`.`payed` 
                from `appointments` 
                inner join `call_ins` on `call_ins`.`id` = `appointments`.`call_id` 
                inner join `patients` on `patients`.`identifier` = `call_ins`.`Identificacion_Paciente` 
                inner join `spaces` on `spaces`.`id` = `appointments`.`space_id` 
                inner join `people` on `people`.`id` = `spaces`.`person_id` 
                inner join `administrators` as `eps` on `eps`.`id` = `patients`.`eps_id` 
               ';
    }

    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS appointment_pendings';
    }
}
