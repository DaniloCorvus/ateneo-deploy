<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\RrhhActivity;
use App\Models\RrhhActivityPerson;
use App\Services\PersonService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RrhhActivityController extends Controller
{
    //
    use ApiResponser;
    public function index()
    {
        $year = 2021;
        return
            $this->success(
                DB::table('rrhh_activities as a')
                    ->join('rrhh_activity_types as t', 't.id', '=', 'a.rrhh_activity_type_id')
                    ->leftJoin('dependencies as d', 'd.id', '=', 'a.dependency_id')
                    ->leftJoin('groups as g', 'g.id', '=', 'd.group_id')
                    ->select(
                        'a.*',
                        'a.id',
                        'a.state',
                      
                        't.name as activity_type',
                        'a.hour_start',
                        'a.hour_end',
                        DB::raw(' IFNULL(g.id,0) as group_id'),
                        DB::raw(' 
                        concat( Date(a.date_start)," " ,a.hour_start) as start,
                        concat( Date(a.date_end)," " ,a.hour_end) as start
                        '),
               

                        DB::raw(' IF (a.dependency_id = "0","Todos",d.name) AS dependency_name'),
                        DB::raw(' IF (a.state = "Anulada","#FF5370",t.color) AS backgroundColor'),
                        DB::raw(' CONCAT(IF (a.state = "Anulada",
                                CONCAT(a.name," (ANULADA)" )
                                ,t.name) 
                                , "-",  IF (a.dependency_id = "0","Todos",d.name) )
                                AS title'),
                    )->whereYear('a.date_start', $year)
                    ->where('a.state', '!=', 'Pendiente')
                    ->orderBy('a.date_start', 'DESC')
                    ->get()
            );
    }

    public function store(Request $request)
    {
        try {

            $data = $request->all();

            $inicio = Carbon::parse($data['date_start']);
            $fin = Carbon::parse($data['date_end']);


            for ($i = $inicio; $i <= $fin; $i->addDay(1)) {
                if (in_array($i->englishDayOfWeek, $data['days'])) {

                    $date1 = $i->format('d M Y');

                    $date2 = $i->format('d M Y');


                    $data['user_id'] = auth()->user()->id;
                    $data['date_start'] = $i->format('Y-m-d');
                    $data['date_end'] = $i->format('Y-m-d');

                    $description =  'Fecha: ' . $date1 . ' : ' . $data['hour_start'] . ' - '
                        . $date2 . ' : ' . $data['hour_end'] . ' Actividad: ' . $data['description'];

                    $activity = RrhhActivity::updateOrCreate(
                        ['id' => $request->get('id')],
                        $data
                    );
                    $idToUpdate =  $request->get('id');
                    if ($idToUpdate) {
                        DB::table('alerts')->where('type', 'Actividad')->where('destination_id', $idToUpdate)->delete();

                        if (count($data['people_id']) > 0) {
                            DB::table('rrhh_activity_people')->where('rrhh_activity_id',  $idToUpdate)->delete();
                        } else {


                            $peopleList = DB::table('rrhh_activity_people')
                                ->where('rrhh_activity_id', $idToUpdate)
                                ->get('id');

                            foreach ($peopleList as $people) {

                                Alert::create(
                                    [
                                        'person_id' => $people->id,
                                        'user_id' => $data['user_id'],
                                        'type' => 'Actividad',
                                        'icon' => 'fa fa-calendar-day',
                                        'title' =>  $data['name'],
                                        'description' => $description,
                                        'modal' =>  1,
                                        'destination_id' => $activity->id
                                    ]
                                );
                            }
                        }
                    }
                    if (count($data['people_id']) > 0 ||  !$idToUpdate) {
                        if (!in_array('0', $data['people_id'])) {

                            foreach ($data['people_id']  as $person_id) {
                                Alert::create(
                                    [
                                        'person_id' => $person_id,
                                        'user_id' => $data['user_id'],
                                        'type' => 'Actividad',
                                        'icon' => 'fa fa-calendar-day',
                                        'title' =>  $data['name'],
                                        'description' => $description,
                                        'modal' =>  1,
                                        'destination_id' => $activity->id
                                    ]
                                );
                                RrhhActivityPerson::create(['rrhh_activity_id' => $activity->id, 'person_id' => $person_id]);
                            }
                        } else {
                            $dataSe = [];
                            if ($data['group_id'] != '0' && $data['dependency_id'] != '0' && in_array('0', $data['people_id'])) {
                                $dataSe = ['dependencies' => [$data['dependency_id']], 'groups' => [$data['group_id']]];
                            }
                            if ($data['group_id'] != '0' && $data['dependency_id'] == '0') {
                                $dataSe = ['dependencies' => [$data['dependency_id']]];
                            }
                            if ($data['group_id'] == '0') {
                                $dataSe = [];
                            }
                            $people =  PersonService::getPeople();

                            /* return response($people); */

                            foreach ($people as $person) {
                                //insertAlert($value["IDENTIFICACION"], $modelo["Fecha_Inicio"], $modelo["Detalles"]);
                                Alert::create(
                                    [
                                        'person_id' => $person->id,
                                        'user_id' => $data['user_id'],
                                        'type' => 'Actividad',
                                        'icon' => 'fa fa-calendar-day',
                                        'title' =>  $data['name'],
                                        'description' => $description,

                                        'modal' =>  1,
                                        'destination_id' => $activity->id
                                    ]
                                );
                                RrhhActivityPerson::create(['rrhh_activity_id' => $activity->id, 'person_id' => $person->id]);
                            }
                        }
                    }
                }
            }



            return $this->success('Guardado con éxito');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error($th->getMessage() . $th->getLine() . $th->getFile(), 500);
        }
    }

    public function getPeople($id)
    {
        return  $this->success(
            RrhhActivityPerson::where('rrhh_activity_id', $id)
                ->with('person', function ($q) {
                    $q->select(
                        'id',
                        DB::raw('CONCAT_WS(" ",first_name,first_surname) as text '),
                    );
                })
                ->get(['id', 'person_id'])
        );
    }


    public function cancel($id)
    {
        $activity = RrhhActivity::find($id);
        $activity->state = 'Anulada';
        $activity->save();
        return  $this->success('Actualizado con éxito');
    }
}
