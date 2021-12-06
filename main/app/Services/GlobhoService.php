<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Cup;
use App\Models\Department;
use App\Models\Level;
use App\Models\Location;
use App\Models\Municipality;
use App\Models\RegimenType;
use App\Models\TypeDocument;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class GlobhoService
{
    /**
     * The base uri to be used to consume the authors service
     * @var string
     */
    public $baseUri;

    /**
     * The apikey to be used to consume the authors service
     * @var string
     */
    public $apikey;


    public function __construct()
    {
        $this->BASE_URI_GLOBO = 'https://mogarsalud.globho.com/api/integration/appointment';

        // $this->baseUri = env('BASE_URI');
        // $this->token = env('TOKEN');
    }

    /**
     * Get the full list of authors from the authors service
     * @return string
     */

    public static function sendGlobho($appointment)
    {

        try {

            repeat:

            $appointment = Appointment::with('space', 'callin')->find($appointment);

            if (!$appointment->space) throw new Exception("Cita Sin spacio asignado " . $appointment->id, 400);
            if (!$appointment->callin->patient) throw new Exception("Cita Sin paciente " . $appointment->id, 400);

            $cup = Cup::find($appointment->procedure);
            $location = Location::find($appointment->callin->patient->location_id);
            $contract = Contract::find($appointment->callin->patient->contract_id);
            $typeDocument =    TypeDocument::find($appointment->callin->patient->type_document_id);
            $regimenType =    RegimenType::find($appointment->callin->patient->regimen_id);
            $level = Level::find($appointment->callin->patient->level_id);
            $municipality = Municipality::find($appointment->callin->patient->municipality_id);
            $department = Department::find($appointment->callin->patient->department_id);
            $company = Company::find($appointment->callin->patient->company_id);


            if (!$cup) throw new Exception("Cita Sin cup service " . $appointment->id, 400);
            if (!$location) throw new Exception("Cita Sin sede " . $appointment->id, 400);
            if (!$contract) throw new Exception("Cita Sin contrato " . $appointment->id, 400);
            if (!$typeDocument) throw new Exception("Cita Sin tipo de documento " . $appointment->id, 400);
            if (!$regimenType) throw new Exception("Cita Sin regimen " . $appointment->id, 400);
            if (!$municipality) throw new Exception("Cita Sin ciudad " . $appointment->id, 400);
            if (!$department) throw new Exception("Cita Sin departamento " . $appointment->id, 400);
            if (!$company) throw new Exception("Cita Sin ips del paciente  " . $appointment->id, 400);

            $appointment->code = $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT);
            $appointment->link = 'https://meet.jit.si/' . $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT);
            $appointment->save();

            if (gettype($level) == 'object' &&     gettype($regimenType) == 'object' && gettype($location) == 'object' && gettype($contract) == 'object') {

                $body = [
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
                        "phone" => $appointment->callin->patient->phone,
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
                    'https://mogarsalud.globho.com/api/integration/appointment/' . "?api_key=$company->code",
                    $body
                );

                if ($response->ok()) {
                    $appointment->on_globo = 1;
                    $appointment->globo_id =  $response->json()['id'];
                    $appointment->save();
                    return  "Migrado... " . $response->json()['id'] . ' ' . $response->json()['message'] ;
                } else {
                    throw new Exception("No migrado: Respuesta globo :   " . json_encode($response->json()) . ' , Contacte con soporte con el codigo : ' . $appointment->id, 400);
                }
            }
        } catch (Exception $e) {
            return  $e->getMessage() ;
            goto repeat;
        }
    }






    public function updateStatus($globoid, $code, $body)
    {
        $response = Http::withOptions(['verify' => false])->put(
            $this->BASE_URI_GLOBO . "/$globoid/state?api_key=$code",
            $body
        );

        return json_encode($response->json());
    }

    public function setStatusCancell($globoid, $code, $body)
     {
        $response = Http::withOptions(['verify' => false])->delete(
            $this->BASE_URI_GLOBO . "/$globoid?api_key=$code",
            $body
        );

        return json_encode($response->json());
    }

}
