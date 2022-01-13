<?php

namespace App\Repositories;

use App\Models\Agendamiento;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\Space;
use App\Services\GlobhoService;
use App\Services\ManagmentAppointmentCreation;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AppointmentRepository
{

    static $globoService;
    static $managmentAppointmentCreation;
    static $agendamientos;
    static $appointmentCreated = [];
    static $appointmentNotCreated = [];
    static $info = [];

    public function __construct(GlobhoService $globoService,   ManagmentAppointmentCreation $managmentAppointmentCreation)
    {
        self::$globoService = $globoService;
        self::$managmentAppointmentCreation = $managmentAppointmentCreation;
        self::$agendamientos = Collect([]);
    }


    public static function store()
    {
        return [
            'appointmentCreated'  => [self::$managmentAppointmentCreation->managment(request()->all())],
            'appointmentNotCreated' => [self::$appointmentNotCreated]
        ];
    }

    public static function show($appointment)
    {
        return Appointment::realtions()->find($appointment);
    }

    public static function cancell($id)
    {
        $reason = Request()->get('reason_cancellation');

        $app = Appointment::find($id);
        $app->state = 'Cancelado';
        $app->date_updated_state = Carbon::now();
        $app->user_modifier = Auth()->user()->id;
        $app->reason_cancellation = $reason;
        // $app->cancellation_at = now();
        $app->save();
        $company = Company::find($app->callin->patient->company_id);

        $space = $app->space;
        $space->status = 1;
        $space->share = $space->share + 1;
        $space->save();

        $body = [

            'state' =>  'Cancelado',
            'anotation' => $reason

        ];;

        $app->globo_response = self::$globoService->setStatusCancell($app->globo_id, $company->code,  $body);
        $app->save();
    }


    public static function pending()
    {
        return  DB::table('appointments')

            ->join('call_ins', 'call_ins.id', 'appointments.call_id')
            ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
            ->join('spaces', 'spaces.id', 'appointments.space_id')
            ->join('people', 'people.id', 'spaces.person_id')
            ->join('administrators as eps', 'eps.id', 'patients.eps_id')
            ->select(
                'appointments.id',
                'eps.name as eps',
                DB::raw('DATE_FORMAT(spaces.hour_start, "%Y-%m-%d %h:%i %p") As date'),
                DB::raw('Concat_ws(" ", people.first_name, people.first_surname) As professional'),
                DB::raw('Concat_ws(" ", patients.firstname,  patients.surname, patients.identifier) As patient'),
                'appointments.price As copago',
                'appointments.observation As description',
                'appointments.state',
                'appointments.payed'
            )
            ->when((Request()->get('patient') && Request()->get('patient') != 'null'), function (Builder $query) {
                $query->where('call_ins.Identificacion_Paciente', Request()->get('patient'));
            })

            ->when((Request()->get('date') &&  Request()->get('date') != 'null'), function (Builder $query) {
                $query->whereDate('spaces.hour_start', Request()->get('date'));
                })
            ->where('appointments.state', '<>', 'Cancelado')

        ->paginate(Request()->get('pageSize', 20), '*', 'page',  Request()->get('page', 1));

    }

    public static function getstatistics()
    {
        $statistics =  Appointment::whereIn('state', ['Confirmado', 'SalaEspera', 'Asistio'])->get();
        $appointmentConfirm = $statistics->where('state', 'Confirmado')->count();
        $appointmentCollection = $statistics->count();
        $appointmentCollectionAll =   $statistics->sum->price;

        return
            [
                'appointmentConfirm' =>  $appointmentConfirm,
                'appointmentCollection' =>  $appointmentCollection,
                'appointmentCollectionAll' => $appointmentCollectionAll,
            ];
    }

    public static function confirm()
    {
        
        
        $app = Appointment::find(request()->get('id'));
        $company = Company::find($app->callin->patient->company_id);
        
        $bodyByGlobho = [

            'state' =>  'Confirmado',
            'anotation' => request()->get('message')

        ];

        
        $app->state = 'Confirmado';
        $app->date_updated_state = Carbon::now();
        $app->user_modifier = Auth()->user()->id;
        $app->message_confirm = request()->get('message');
        $app->globo_response = self::$globoService->updateStatus($app->globo_id, $company->code,  $bodyByGlobho);
        return $app->save();

    }

    public static function recurrent()
    {
        $data = request()->all();
        $days =  request()->get('daysRecurrente');
        $dateStart = Carbon::parse(request()->get('date_startRecurrente'));
        $dateEnd = Carbon::parse(request()->get('date_endRecurrente'));

        $space = Space::findOrfail(request()->get('space'));

        $agendamiento = Agendamiento::findOrfail($space->agendamiento_id);

        self::$agendamientos = Agendamiento::join('spaces', 'spaces.agendamiento_id',  'agendamientos.id')
            ->whereRaw("DATE_FORMAT(spaces.hour_start, '%H:%i:%s')" . ' = "' .  Carbon::parse($space->hour_start)->format('H:i:s') . '"')
            ->where('agendamientos.person_id', $agendamiento->person_id)
            ->where('spaces.state', 'Activo')
            ->where('spaces.status', true)
            ->orderBy('spaces.hour_start', 'Asc')
            ->get(['spaces.id', 'spaces.hour_start']);


        for ($day = $dateStart; $day <= $dateEnd; $day->addDay(1)) {

            if (in_array($day->englishDayOfWeek,  $days)) {
                self::verifySpace($day, $space, $data);
            }
        }

        return ['appointmentCreated'  => self::$appointmentCreated,   'appointmentNotCreated' => self::$appointmentNotCreated];
    }

    public static function verifySpace($i, $space, $data)
    {
        $res = self::$agendamientos->firstWhere('hour_start',  Carbon::parse($i->format('Y-m-d')  .  ' ' . Carbon::parse($space->hour_start)->format('H:i:s'))->format('Y-m-d H:i:s'));

        if ($res) {
            $data['space'] = $res->id;

            $info = self::$managmentAppointmentCreation->managment($data);

            if ($info) {
                array_push(self::$appointmentCreated, $info);
            } else {
                array_push(self::$appointmentNotCreated, 'Cita no creada para el dia ' . Carbon::parse($i->format('Y-m-d')  .  ' ' . Carbon::parse($space->hour_start)->format('H:i:s')));
            }
        } else {
            array_push(self::$info, 'Cita no creada para el dia ' . Carbon::parse($i->format('Y-m-d')  .  ' ' . Carbon::parse($space->hour_start)->format('H:i:s')));
        }
    }
}
