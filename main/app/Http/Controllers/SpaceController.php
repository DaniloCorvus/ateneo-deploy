<?php

namespace App\Http\Controllers;

use App\HistoryAgendamiento;
use App\Models\Person;
use App\Services\SpaceService;
use App\Models\Space;
use App\Models\Speciality;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaceController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function indexCustom($idSpeciality, $person = 0)
    {
        $spaces = collect([]);
        $persons = Person::query();
        $persons->when($person && $person != 0 && $person != 'undefined',  function ($q) use ($person) {
            $q->where('id', $person);
        });

        $persons->whereHas('specialties',  function ($q)  use ($idSpeciality) {
            $q->where('id', $idSpeciality);
        })->with('specialties');

        $data = $persons->pluck('id');
        $spaces = DB::table('spaces')->where('person_id', [$data])->where('status', [true])->get(['id', 'hour_start As start', 'hour_end As end', 'status', 'backgroundColor', 'className']);
        return  $this->success($spaces);
    }

    public function index()
    {
        try {
            $params = Request()->all();
            $spaces = collect([]);

            if (!isset($params['departemIdPatient'])) throw new Exception('el paciente no tienen un departamento o regional asociado', 400);

            $departemIdPatient = $params['departemIdPatient'];
            $spaces = DB::table('spaces as s')
                ->join('agendamientos as a', 'a.id', '=', 's.agendamiento_id')
                ->leftJoin('locations', 'locations.id', 'a.location_id')
                ->join('people as p', 'p.id', '=', 's.person_id')
                ->where('a.type_agenda_id', $params['type_agenda_id'])
                ->where('a.type_appointment_id', $params['type_appointment_id'])
                ->where('a.speciality_id', $params['speciality_id'])
                ->where('s.hour_start', '>', DB::raw('now()'))
                ->where('s.status', [true])
                ->where('s.state', 'Activo')
                // ->where(function ($q) use ($departemIdPatient) {
                //     $q->whereRaw(
                //         "
                //           CASE WHEN a.department_id   = $departemIdPatient  THEN  a.department_id =  $departemIdPatient AND   s.type =  'Regional'
                //                       WHEN a.department_id != $departemIdPatient  THEN  s.type =  'Nacional'
                //           END
                //          "
                //     );
                // })

                ->when(array_key_exists('company_id', $params), function ($q) use ($params) {
                    $q->where('a.ips_id', '=', $params['company_id']);
                })
                ->when(array_key_exists('location_id', $params), function ($q) use ($params) {
                    $q->where('a.location_id', '=', $params['location_id']);
                })
                ->when((array_key_exists('person_id', $params) && $params['person_id']), function ($q) use ($params) {
                    $q->where('a.person_id', $params['person_id']);
                })
                ->get([
                    's.id', 's.hour_start As start', 's.hour_end As end', 'a.id as aid', 's.status', 'p.color as backgroundColor',
                    's.className',
                    'p.first_name as title',
                    'locations.address'

                ]);
            return  $this->success($spaces);
        } catch (\Throwable $th) {
            return  $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Space  $space
     * @return \Illuminate\Http\Response
     */
    public function show(Space $space)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Space  $space
     * @return \Illuminate\Http\Response
     */
    public function edit(Space $space)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Space  $space
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Space $space)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Space  $space
     * @return \Illuminate\Http\Response
     */
    public function destroy(Space $space)
    {
        //
    }
    public function cancel(Request $req)
    {
        try {

            $space = Space::findOrFail($req->id);
            $space->state = 'Cancelado';
            $space->save();


            HistoryAgendamiento::create([
                'agendamiento_id' =>  $space->agendamiento_id,
                'user_id' => auth()->user()->id,
                'description' => 'Espacio cancelado: ' . $space->hour_start,
                'icon' => 'ri-close-circle-line'
            ]);
            return $this->success('Actualización exitosa');
        } catch (\Throwable $th) {
            return $this->error('Ha ocurrido un error', 400);
        }
    }
    public function  statistics()
    {
        $spaceService = new SpaceService();
        return $this->success($spaceService->statistics());
    }
    public function  statisticsDetail()
    {
        $spaceService = new SpaceService();
        return $this->success($spaceService->statisticsDetail());
    }
}
