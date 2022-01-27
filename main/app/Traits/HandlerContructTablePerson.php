<?php

namespace App\Traits;

use App\Models\Patient;
use App\Models\Person;
use App\Models\Profesional;
use App\Services\SpecialitysDoctorsService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

trait HandlerContructTablePerson
{
    public $specialitysDoctorsService;


    public function handlerInsertTable($data)
    {

        // $specialitysDoctorsService = new SpecialitysDoctorsService();

        foreach ($data as  $item) {
            if (gettype($item) != 'array') {
                dd('Necesitas un array');
            } else {
                $dataFormated = [];
                foreach ($item as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        $this->handlerInsertTableRespaldo($value, $index);
                    } else {
                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
            }

            // dd(1, $dataFormated);

            // date_default_timezone_set('America/Bogota');
            //date
            // $mil = 1626025200000;
            // start_date
            // $mil = 1626029045436;
            //end_date
            // $mil = 1625860162852;

            // 0 => "13:44:05"
            // 1 => "11-07-2021"

            // 0 => "14:49:22"
            // 1 => "09-07-2021"

            // $date = (int)(preg_replace('/([^0-9]+)/', "", '1627621200000'));
            // $start_date = (int)(preg_replace('/([^0-9]+)/', "", '1627646400000'));
            // $end_date = (int)(preg_replace('/([^0-9]+)/', "", '1627664400000'));

            // $date = $date / 1000;
            // $start_date = $start_date / 1000;
            // $end_date = $end_date / 1000;

            // dd([date("d-m-Y H:i:s", $date), date("d-m-Y H:i:s", $end_date), date("d-m-Y H:i:s", $start_date), gmdate('Y-m-d h:i:s \G\M\T', $date), date("Y-m-d H:i:s", $date)]);

            // Profesional::create($dataFormated);
            // $this->handlerInsertTableDepend($item['Person']);
            // json_decode($specialitysDoctorsService->get($dataFormated['username']), true);

            // foreach (json_decode($specialitysDoctorsService->get($dataFormated['username']), true) as $sede) {
            //     $this->handlerInsertTableRelationship($sede['ID'], $dataFormated['id']);
            // }
        }
    }

    public function handlerInsertTableDepend($data)
    {
        $dataFormated = [];
        foreach ($data as  $index => $item) {
            if (gettype($item) != 'array') {
                $dataFormated[customSnakeCase($index)] = $item;
            }
        }
        Person::create($dataFormated);
    }

    public function handlerInsertTableRelationship($data, $user)
    {
        DB::insert('insert into locations_professional (professional_id, location_id) values (?, ?)', [$data, $user]);
    }

    public function handlerInsertTableRespaldo($data, $table)
    {

        if (count($data) > 0) {

            if (
                $table != 'EPSs'
                && $table != 'Interface'
                && $table != 'Parent'
                && $table != 'Room'
                && $table != 'Speciality'
                // && $table != 'Patient'
                && $table != 'Order'
                && $table != 'AssignedDr'
                && $table != 'Contract'
                && $table != 'Exam'
                && $table != 'ExamGroup'
                && $table != 'Regional'
                && $table != 'Roles'
                && $table != 'Specialities'
                && $table != 'Person'
            ) {
                //AssignedDr; 
                echo json_encode($data['Person']);
                echo ('<br>');
                // dd($data['Person']);
                // $index = strpos($data['Person']['Identifier'], '^') + strlen('^');
                // $result = substr($data['Person']['Identifier'], $index);

                // dd(['Otro array', $result]);

                $dataFormated = [];
                foreach ($data as $index =>  $value) {
                    if (gettype($value) == 'array') {
                        // dd(['Otro array',  $index,  $value]);
                    } else {
                        $dataFormated[customSnakeCase($index)] = $value;
                    }
                }
                // dd($dataFormated);
                // Person::create($dataFormated);
            }
        }
    }
}
