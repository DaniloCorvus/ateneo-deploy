<?php

namespace App\Services;

use App\HistoryAppointment;
use App\Listeners\SendAppointmentCreatedNotification;
use App\Listeners\SendAppointmentModifiedNotification;
use App\Models\Appointment;
use App\Models\CallIn;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Cup;
use App\Models\Department;
use App\Models\Globho;
use App\Models\Level;
use App\Models\Location;
use App\Models\Municipality;
use App\Models\Person;
use App\Models\RegimenType;
use App\Models\Space;
use App\Models\TypeDocument;
use App\Models\WaitingList;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Elibom\APIClient\ElibomClient;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManagmentAppointmentCreation
{

    use ApiResponser;

    private   $data;
    private   $space;
    private   $waitingList;
    private   $another;
    private   $company;
    private   $appointment;
    private   $dataByMessages =  null;
    private   $reducer = 1;
    private   $response = 'No migrado';
    private   $body = 'No migrado';

    public function __construct(
        globhoService $globhoService,
        SendAppointmentModifiedNotification $sendAppointmentModifiedNotification,
        SendAppointmentCreatedNotification $sendAppointmentCreatedNotification
    ) {
        $this->globhoService = $globhoService;
        $this->EMAIL_ELIBOM = 'app@sevicol.com.co';
        $this->PASS_ELIBOM = 't77Mp35gEu';
        $this->BASE_URI_GLOBO = 'https://mogarsalud.globho.com/api/integration/appointment';
        $this->sendAppointmentModifiedNotification = $sendAppointmentModifiedNotification;
        $this->sendAppointmentCreatedNotification = $sendAppointmentCreatedNotification;
        Carbon::setlocale(LC_TIME, 'es_ES.utf8');
    }

    public function managment($request)
    {
        try {

            $this->data = $request;
            $this->another = (isset($this->data['anotherData'])) ? $this->data['anotherData'] : [];

            $this->company = Company::find($this->data['patient']['company_id']);

            switch ((bool)isset($this->data['anotherData']['currentAppointment'])) {
                case true:
                    $this->appointment =  Appointment::find($this->data['anotherData']['currentAppointment']);

                    if (isset($this->data['space'])) {
                        $this->space = Space::with('person')->findOrfail($this->data['space']);
                        $this->appointment->space_id =   $this->space->id;
                        $this->appointment->save();
                    }

                    $this->getDataAppointment($this->appointment->id);
                    $waitingList =  WaitingList::firstWhere('appointment_id', $this->appointment->id);
                    $waitingList->state = 'Agendado';
                    $waitingList->save();
                    $this->getDataAppointment($this->appointment->id);
                    break;

                case false:
                    $this->updateCall();
                    $this->createAppointment();
                    $this->getDataAppointment($this->appointment->id);
                    break;
            }

            if (!isset($this->data['space'])) {
                $this->CreateWailist($this->appointment->id);
            }

            if (isset($this->data['space'])) {
                $this->space = Space::with('person', 'person.company')->findOrfail($this->data['space']);

                if ($this->space->status <= 0 || $this->space->state == 'Cancelado') {
                    throw new Exception("Este espacio ya no se encuentra disponible" . $this->space->hour_start);
                }


                $this->space->share = $this->space->share - $this->reducer;

                if ($this->space->share <= 0  ) {
                    $this->space->status = 0;
                }

                $this->space->saveOrFail();
                $this->appointment->code = $this->company->simbol . date("ymd", strtotime($this->space->hour_start)) . str_pad($this->appointment->id, 7, "0", STR_PAD_LEFT);
                $this->appointment->link = 'https://meet.jit.si/' . $this->company->simbol . date("ymd", strtotime($this->appointment->space->hour_start)) . str_pad($this->appointment->id, 7, "0", STR_PAD_LEFT);
                $this->appointment->saveOrFail();


                if ($this->space->person->to_globo) {
                    $this->response =  $this->reportToGlobo($this->appointment->id);
                }


                HistoryAppointment::create([
                    'appointment_id' =>  $this->appointment->id,
                    'user_id' => auth()->user()->id,
                    'description' => json_encode($this->body)
                ]);


                Log::info([
                    'appointment_id' => $this->appointment->id . ' :heart: ',
                    'message' => $this->sendMessage($this->appointment, $this->space, $this->data, $this->company),
                    'User' => (auth()->user()) ? auth()->user()->usuario : 'Sin usuario',
                    'body' => json_encode($this->body),
                    'Globo' => ($this->response != 'No migrado' && $this->response != 'null' && $this->response != null) ? $this->response->json() : 'No migrado'
                ]);

                $this->sendAppointmentCreatedNotification->handleMail($this->appointment, $this->space,  $this->data, $this->another);
            }

            return [
                'space' => $this->space, 'waitingList' =>  $this->waitingList,
                'patient' => $this->data['patient'], 'anotheData' => $this->data['anotherData'],
                'waitingList' =>  $this->waitingList,
                'appointment' => $this->appointment,
                'info' => ($this->dataByMessages) ? $this->dataByMessages : ''
            ];
        } catch (\Throwable $th) {
            Log::error(json_encode([' message  ' => ':boom:' . $th->getMessage(), '  file  ' =>  $th->getFile(), '  line  ' => $th->getLine(), 'Usuario' => auth()->user()->id]));
            return [$th->getMessage(),  $th->getFile(), $th->getLine(), 'Usuario' => auth()->user()->id];
        }
    }


    public function sendMessage()
    {

        return 'Sin mensaje';

        // $elibom = new ElibomClient($this->EMAIL_ELIBOM, $this->PASS_ELIBOM);

        // $this->message  = $this->data['patient']['firstname'] . ', se ha ';

        // $this->message .= (isset($this->space->id)) ? 'agendado una cita ' : 'a単adido a lista de espera en la modalidad ';


        // $this->message .=  (isset($this->appointment->space)) ? $this->appointment->space->agendamiento->typeAppointment->description : '';

        // $this->message .=  (isset($this->space->id)) ? ' el dia ' : '';

        // $this->message .=  (isset($this->space->id)) ? $this->space->hour_start : '';

        // $this->message .=  (isset($this->space->id) && ($this->appointment->space->agendamiento->typeAppointment->face_to_face)) ?

        //     ' EN SEDE ' . $this->dataByMessages['location']['name'] . ' ubicada en ' . $this->dataByMessages['location']['address']
        //     :
        //     ' EN LA SALA VIRUTAL DE LA CENTRAL DE ESPECIALISTAS ';

        // return  $elibom->sendMessage($this->data['patient']['phone'], $this->message);
    }

    public function createAppointment()
    {
        DB::transaction(function () {
            $this->appointment = Appointment::create([
                'call_id' => isset($this->data['call']['id']) ? $this->data['call']['id'] : null,
                'space_id' => isset($this->data['space']) ? $this->data['space'] : null,
                'diagnostico' => $this->data['diagnosticoId']['value'],
                'profesional' => $this->data['person_remisor'],
                'ips' => $this->data['ips_remisor'],
                'speciality' =>  $this->data['especiality'],
                'code' => '',
                'date' =>  $this->data['date_remisor'],
                'origin' =>  isset($this->data['call']['id']) ? 'LLamada' : 'Linea de frente',
                'procedure' =>  $this->data['procedureId']['value'],
                'price' => $this->getPriceCuote(),
                'observation' =>  $this->data['observation'],
                'link' => '',
            ]);
        });
    }

    public function CreateWailist($id)
    {
        $this->waitingList = WaitingList::create([
            'type_appointment_id' => $this->another['appointmentId'],
            'sub_type_appointment_id' => $this->another['subappointmentId'],
            'speciality_id' => $this->another['speciality'],
            'state' => 'Pendiente',
            'appointment_id' => $id
        ]);
    }

    public function updateCall()
    {
        $call = CallIn::find($this->data['call']['id']);
        $call->Tipo_Servicio = isset($this->data['tipification']['type_service_id']) ? $this->data['tipification']['type_service_id'] : null;
        $call->Tipo_Tramite = isset($this->data['tipification']['formality_id']) ? $this->data['tipification']['formality_id'] : null;
        $call->Ambito =  isset($this->data['tipification']['ambit_id']) ? $this->data['tipification']['ambit_id'] : null;
        $call->Identificacion_Paciente =  $this->data['patient']['identifier'];
        $call->saveOrFail();
    }

    public function getDataAppointment()
    {

        $appointmentData = Appointment::with(
            'callIn',
            'callIn.patient',
            'callIn.patient.typeDocument',
            'callIn.patient.RegimenType',
            'callIn.patient.contract',
            'callIn.patient.level',
            'space'

        )->find($this->appointment->id);


        $cup = Cup::find($appointmentData->procedure);

        if (findingKey($appointmentData->space)) {

            $this->space = Space::with('agendamiento', 'agendamiento.company',  'agendamiento.typeAppointment', 'agendamiento.location')
                ->find($appointmentData->space->id);

            $this->dataByMessages = [
                "id" => $appointmentData->id,
                "startDate" => Carbon::parse($this->space->hour_start)->locale('es_Es')->isoFormat('LLLL'),
                "endDate" => Carbon::parse($this->space->hour_end)->locale('es_Es')->isoFormat('LLLL'),
                "state" => "Asignado",
                "type" =>      $this->space->agendamiento->typeAppointment->description,
                "text" => $appointmentData->observation,
                "telehealthUrl" => $appointmentData->link,
                "ConfirmationUrl" => "",
                "appointment" => $appointmentData->code,
                "patient" => [
                    "id" => $appointmentData->callIn->patient->identifier,
                    "identificationType" => $appointmentData->callIn->patient->typeDocument->code,
                    "firstName" => $appointmentData->callIn->patient->firstname,
                    "secondName" =>  $appointmentData->callIn->patient->middlename,
                    "firstlastName" => $appointmentData->callIn->patient->surname,
                    "secondlastName" => $appointmentData->callIn->patient->secondsurname,
                    "email" => $appointmentData->callIn->patient->email,
                    "phone" => $appointmentData->callIn->patient->phone,
                    "birthDate" => $appointmentData->callIn->patient->date_of_birth,
                    "gender" =>  $appointmentData->callIn->patient->gener,
                    "codeRegime" => $appointmentData->callIn->patient->regimenType->code,
                    "categoryRegime" => $appointmentData->callIn->patient->level->code,
                ],
                'service' => [
                    'id' => $cup->code,
                    'name' => $cup->description,
                    'recomendations' => $cup->recomendation
                ],
                'doctor' => [
                    'id' =>  $this->space->person->identifier,
                    'name' => $this->space->person->full_name,
                    'company' => [
					'id' => ($appointmentData->space->person->company) ? $appointmentData->space->person->company->tin : '',
					'name' => ($appointmentData->space->person->company) ? $appointmentData->space->person->company->name : ''
                  ],
                ],
                'agreement' => [
                    'id' => $appointmentData->callIn->patient->contract->number,
                    'name' => $appointmentData->callIn->patient->contract->name
                ],
                'location' => [
                    'id' =>  findingKey($this->space->agendamiento->location) ? $this->space->agendamiento->location->id : null,
                    'name' => findingKey($this->space->agendamiento->location) ? $this->space->agendamiento->location->name : null,
                    'address' => findingKey($this->space->agendamiento->location) ? $this->space->agendamiento->location->address : null
                ],
                'company' => findingKey($this->space->agendamiento->company) ? $this->space->agendamiento->company : null,
            ];
        }
    }

    public function getPriceCuote()
    {
        $level  = Level::find($this->data['patient']['level_id']);
        if ($level) {
            if ($this->data['patient']['regimen_id']  == 2) {
                return 0;
            }
            return $level->cuote;
        } else {
            $level  = Level::max('cuote');
            return   $level;
        }
    }

    public function reportToGlobo($id)
    {

        $appointment = Appointment::with('space', 'callin')->find($id);

        if ($appointment->space && $appointment->callin->patient) {

            $cup = Cup::find($appointment->procedure);
            $location = Location::find($appointment->callin->patient->location_id);
            $contract = Contract::find($appointment->callin->patient->contract_id);
            $typeDocument =    TypeDocument::find($appointment->callin->patient->type_document_id);
            $regimenType =    RegimenType::find($appointment->callin->patient->regimen_id);
            $level = Level::find($appointment->callin->patient->level_id);
            $municipality = Municipality::find($appointment->callin->patient->municipality_id);
            $department = Department::find($appointment->callin->patient->department_id);
            $company = Company::find($appointment->callin->patient->company_id);

            if ($company) {

                $appointment->code = $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT);
                $appointment->link = 'https://meet.jit.si/' . $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT);
                $appointment->save();

                if (gettype($level) == 'object' &&     gettype($regimenType) == 'object' && gettype($location) == 'object' && gettype($contract) == 'object') {

                   $this->body =  $body = [
                        "id" => 0,
                        "startDate" => Carbon::parse($appointment->space->hour_start)->format('Y-m-d H:i'),
                        "endDate" => Carbon::parse($appointment->space->hour_end)->format('Y-m-d H:i'),
                        "state" => $appointment->state,
                        "type" => ($appointment->space->agendamiento->typeAppointment->description == 'TELEMEDICINA') ? 4 : 1,
                        "text" => $appointment->observation,
                        "telehealthUrl" => 'https://meet.jit.si/' . $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT),
                        "ConfirmationUrl" => "",
                        "appointmentId" => $appointment->code,
                        "patient" => [
                            "id" => $appointment->callin->patient->identifier,
                            "identificationType" => $typeDocument->code,
                            "firstName" => $appointment->callin->patient->firstname,
                            "secondName" =>  $appointment->callin->patient->middlename,
                            "firstlastName" => $appointment->callin->patient->surname,
                            "secondlastName" => $appointment->callin->patient->secondsurname,
                            "email" => $appointment->callin->patient->email,
                            "phone" => $appointment->callin->patient->phone .' - ' .  $appointment->callin->patient->optional_phone,
                            "birthDate" => $appointment->callin->patient->date_of_birth,
                            "gender" =>  $appointment->callin->patient->gener,
                            "codeRegime" => $regimenType->code,
                            "categoryRegime" => $level->code,
                            "codeCity" => substr($municipality->code, 2, 5),
                            "codeState" => $department->code,
                        ],

                        'service' => [
                            'id' => $cup->code,
                            'name' => $cup->description,
                            'recomendations' => $cup->recomendation
                        ],
                        'doctor' => [
                            'id' =>  $appointment->space->person->identifier,
                            'name' => $appointment->space->person->full_name,
                            'company' => [
                					'id' => ($appointment->space->person->company) ? $appointment->space->person->company->tin : '',
                					'name' => ($appointment->space->person->company) ? $appointment->space->person->company->name : ''
                            ],
                        ],
                        'agreement' => [
                            'id' => $contract->number,
                            'name' => $contract->name
                        ],
                        'location' => [
                            'id' => $location->globo_id,
                            'name' => $location->name
                        ],

                    ];

                    $response = Http::withOptions([
                                                'verify' => false,
                                            ])
                        ->post(
                        'https://mogarsalud.globho.com/api/integration/appointment' . "?api_key=$company->code",
                        $body
                    );

                    if ($response->ok()) {
                        $appointment->on_globo = 1;
                        $appointment->globo_id =  $response->json()['id'];
                        $appointment->save();
                        return $response;
                    } else {
                        return 'No migrado';
                    }
                }
            }
        }
    }
}
