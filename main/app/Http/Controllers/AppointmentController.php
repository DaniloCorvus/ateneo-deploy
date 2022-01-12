<?php

namespace App\Http\Controllers;

use App\Events\ModifiedAppointment;
use App\Models\Agendamiento;
use App\Models\Appointment;
use App\Models\CallIn;
use App\Http\Requests\SaveAppointmentRequest;
use App\Http\Requests\AppointmentRequest;
use App\Models\Call;
use App\Models\Contract;
use App\Models\Company;
use App\Models\Level;
use App\Models\Cup;
use App\HistoryAppointment;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Space;
use App\Models\TypeDocument;
use App\Traits\ApiResponser;
use App\Models\WaitingList;
use App\Services\AppointmentService;
use App\Services\globhoService;
use App\Services\ManagmentAppointmentCreation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use stdClass;
use App\Models\Globho as Globho;
use Exception;

use App\Listeners\SendAppointmentModifiedNotification;
use App\Listeners\SendAppointmentCreatedNotification;

// include($_SERVER['DOCUMENT_ROOT'] .  DIRECTORY_SEPARATOR . '../elibom' . DIRECTORY_SEPARATOR . 'src/elibom_client.php');
include($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'main'  . DIRECTORY_SEPARATOR . 'elibom' . DIRECTORY_SEPARATOR . 'src/elibom_client.php');

use Elibom\APIClient\ElibomClient as ElibomClient;
use Illuminate\Database\Query\Builder;

class AppointmentController extends Controller
{

    private $globhoService;
    private $globho;
    private $EMAIL_ELIBOM;
    private $PASS_ELIBOM;
    private $message = '';
    private $sendAppointmentModifiedNotification;
    private $sendAppointmentCreatedNotification;
    private $managmentAppointmentCreation;

    public function __construct(
        globhoService $globhoService,
        SendAppointmentModifiedNotification $sendAppointmentModifiedNotification,
        SendAppointmentCreatedNotification $sendAppointmentCreatedNotification,
        ManagmentAppointmentCreation $managmentAppointmentCreation
    ) {
        $this->globhoService = $globhoService;
        $this->EMAIL_ELIBOM = 'app@sevicol.com.co';
        $this->PASS_ELIBOM = 't77Mp35gEu';
        $this->BASE_URI_GLOBO = 'https://mogarsalud.globho.com/api/integration/appointment';
        $this->sendAppointmentModifiedNotification = $sendAppointmentModifiedNotification;
        $this->sendAppointmentCreatedNotification = $sendAppointmentCreatedNotification;
        $this->managmentAppointmentCreation = $managmentAppointmentCreation;
    }

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->success(AppointmentService::index());
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentRequest $request)
    {
        return $this->managmentAppointmentCreation->managment(request()->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($appointment)
    {        //
        $app =  Appointment::with(
            [

                'callIn' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient.company' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient.eps' => function ($q) {
                    $q->select('*');
                },

                'callIn.formality' => function ($q) {
                    $q->select('*');
                },

                'space' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento.person' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento.speciality' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.subTypeAppointment' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.typeAppointment' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.company' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.location' => function ($q) {
                    $q->select('*');
                }
            ]
        )->find($appointment);
        return response()->json($app);
    }

    public function getDataCita($id)
    {
        Patient::findOrfail($id);
    }

    public function cleanInfo($data)
    {
        $callIn = CallIn::findOrfail($data);
        $callIn->status = 'Atendida';
        $callIn->save();
        return $this->success('Finalizado');
    }

    public function cancel($id)
    {
        try {
            $reason = Request()->get('reason_cancellation');

            $app = Appointment::find($id);
            $app->state = 'Cancelado';
            $app->reason_cancellation = $reason;
            $app->cancellation_at = now();
            $app->save();
            $company = Company::find($app->callin->patient->company_id);

            $space = $app->space;
            $space->status = 1;
            $space->save();

            $body = [

                'state' =>  'Cancelado',
                'anotation' => $reason

            ];

            $response = Http::delete(
                $this->BASE_URI_GLOBO . "/$app->globo_id?api_key=$company->code",
                $body
            );

            $app->globo_response = json_encode($response->json());
            $app->save();


            return $this->success('La cita se ha cancelado con èxito');
        } catch (\Throwable $th) {
            return $this->error('Ha ocurrido un error' . $th->getMessage(), 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function updateFromGlobho(Request $request, $code, $state)

    {
        try {

            if (!in_array($state, ['Agendado', 'Cancelado', 'Pendiente', 'SalaEspera', 'Asistio', 'Confirmado', 'NoAsistio']) && $code) {
                throw new Exception("No Existe codigo de appointment o estado");
            }

            $appointment = Appointment::with('space', 'callin', 'callin.patient', 'space.agendamiento')->firstWhere('code', $code);

            if (!isset($appointment)) {
                throw new Exception('No se logra encontrar appointment');
            }

            $appointment->state = $state;
            $appointment->saveOrfail();

            if (in_array($state, ['Agendado', 'Cancelado', 'Asistio'])) {
                $this->sendAppointmentModifiedNotification->handleMail($appointment);
            }

            return $this->success('Actualizacion correcta');
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }
    public function createFromGlobho(Request $request)

    {
        try {

            $appointment =  Appointment::create([
                'diagnostico' => $request->get('diagnostico'),
                'profesional' => $request->get('profesional'),
                'ips' => $request->get('ips'),
                'speciality' => $request->get('speciality'),
                'code' => $this->getCode($request->get('tin')),
                'link' =>  '',
                'date' => $request->get('date'),
                'origin' =>     'Globo',
                'procedure' =>  $request->get('procedure'),
                'price' => '',
                'observation' => $request->get('observation'),

            ]);

            return $this->success(['Creacion correcta', $appointment], 201);
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(),  $th->getFile(), $th->getLine()], 400);
        }
    }

    public function getCode($tin)
    {
        $company = Company::firstWhere('tin', $tin);
        return $company->simbol;
    }

    public function getPending()
    {
        return response()->success(
            DB::table('appointments')
                ->join('call_ins', 'call_ins.id', 'appointments.call_id')
                ->join('patients', 'patients.identifier', 'call_ins.Identificacion_Paciente')
                ->join('spaces', 'spaces.id', 'appointments.space_id')
                ->join('people', 'people.id', 'spaces.person_id')
                ->join('administrators as eps', 'eps.id', 'patients.eps_id')
                ->select(
                    'eps.name as eps',
                    DB::raw('DATE_FORMAT(spaces.hour_start, "%Y-%m-%d %h:%i %p") As date'),
                    DB::raw('Concat_ws(" ", people.first_name, people.first_surname) As professional'),
                    DB::raw('Concat_ws(" ", patients.firstname,  patients.surname, ' . DB::raw("FORMAT(patients.identifier, 0, '.')") . ' ) As patient'),
                    'appointments.price As copago',
                    'appointments.observation As description',
                    'appointments.state'
                )
                ->when((Request()->get('patient') && Request()->get('patient') != 'null'), function (Builder $query) {
                    $query->where('call_ins.Identificacion_Paciente', Request()->get('patient'));
                })
                ->when((Request()->get('date') &&  Request()->get('date') != 'null'), function (Builder $query) {
                    $query->whereDate('spaces.hour_start', Request()->get('date'));
                })
                ->orderBy('spaces.hour_start', 'Asc')
                ->paginate(Request()->get('pageSize', 20), '*', 'page',  Request()->get('page', 1))
        );
    }

    public function getstatisticsByCollection()
    {
        $statistics =  Appointment::whereIn('state', ['Confirmado', 'SalaEspera', 'Asistio'])->get();

        $appointmentConfirm = $statistics->where('state', 'Confirmado')->count();

        $appointmentCollection = $statistics->count();

        $appointmentCollectionAll =   $statistics->sum->price;

        return response()->success(
            [
                'appointmentConfirm' =>  $appointmentConfirm,
                'appointmentCollection' =>  $appointmentCollection,
                'appointmentCollectionAll' => $appointmentCollectionAll,
            ]
        );
    }

    public function confirmAppointment()
    {
        return $this->success(
            Appointment::whereId(request()->get('id'))->update(
                [
                    'state' => 'Cancelado',
                    'message_confirm' => request()->get('message')
                ]
            )
        );
    }
}
