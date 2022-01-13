<?php

namespace App\Http\Controllers;

use App\Models\Agendamiento;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Cup;
use App\Models\Department;
use App\Models\Level;
use App\Models\Location;
use App\Models\Municipality;
use App\Models\Person;
use App\Models\RegimenType;
use App\Models\Space;
use App\Models\TypeAppointment;
use App\Models\TypeDocument;
use App\Models\WaitingList;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function spacesTomados()
    {
        $person = Person::find(11632);
        $verifyDate =  '10:20:00';

        $result = false;
        $agendamientos = Agendamiento::with('spaces')->where('person_id', 11632)
            ->whereBetween('date_start', ['2021-08-26', '2021-08-26'])
            ->orWhereBetween('date_end',  ['2021-08-26', '2021-08-26'])->get();





        dd($agendamientos);


        foreach ($agendamientos as $agendamiento) {
            foreach ($agendamiento->spaces as $space) {
                if (Carbon::parse($verifyDate)->betweenExcluded($space->hour_start, $space->hour_end)) {
                    $result = true;
                    break;
                }
            }
        }

        dd($result);

        // 2021-08-19
        // date_start

        // hour_end: "07:00"
        // hour_start: "19:00"

        // $inicio = Carbon::parse();
        // $fin = Carbon::parse();

        dd([
            Carbon::parse('2021-08-18' . '07:00') <
                Carbon::parse('2021-08-19' . '19:00')
        ]);

        return response([
            Carbon::parse('2021-08-18' . '07:00'),
            Carbon::parse('2021-08-19' . '19:00'),
        ]);

        // $data = [];
        // $data['date_start'] = Carbon::parse("2021-08-30 08:15:00");
        // $data['date_end'] = Carbon::parse("2021-08-30 08:30:00");

        // // dd(
        // //     Carbon::parse('2021-08-30 08:20:00')->betweenExcluded($data['date_start'], $data['date_end']),
        // //     Carbon::parse('2021-08-30 08:30:00')->betweenExcluded($data['date_start'], $data['date_end'])
        // // );

        // $result = DB::table('spaces')
        //     ->WhereBetween('hour_start', [$data['date_start'], $data['date_end']])
        //     ->orWhere(function ($q) use ($data) {
        //         $q->whereDate('hour_end', '>=', $data['date_start'])
        //             ->whereDate('hour_end', '<=', $data['date_end']);
        //     })
        //     ->first();

        // dd($result);


        // DB::table('spaces')->whereBetween('hour_start', [$data['date_end'], $data['date_start']])->orWhereBetween('hour_end', [$data['date_end'], $data['date_start']])->first();
    }
    public function fillDdays($agendamiento, $date)
    {
        $person = Person::find($agendamiento->person_id);
        $typeAppointment = TypeAppointment::find($agendamiento->type_agenda_id);

        Space::create(
            [
                'agendamiento_id' => $agendamiento->id,
                'status' => true,
                'hour_start' => (string)$date,
                'hour_end' => (string)$date->addMinutes($agendamiento->long),
                'long' => $agendamiento->long,
                'person_id' => $agendamiento->person_id,
                'backgroundColor' => $person->color,
                'className' => $typeAppointment->icon

            ]
        );
    }

    public function createSpaces()
    {
        try {
            $agendamiento = Agendamiento::find(732);
            $inicio = Carbon::parse($agendamiento->date_start);
            $fin = Carbon::parse($agendamiento->date_end);
            for ($i = $inicio; $i <= $fin; $i->addDay(1)) {
                if (in_array($i->englishDayOfWeek, $agendamiento->days)) {
                    for (
                        $space = Carbon::parse($i->format('Y-m-d') . $agendamiento->hour_start);
                        $space < Carbon::parse($i->format('Y-m-d') . $agendamiento->hour_end);
                        $space->addMinutes($agendamiento->long)
                    ) {
                        $this->fillDdays($agendamiento, $space->copy());
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function conection()
    {
        try {
            // dd(DB::connection()->getPdo());
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e);
        }
    }
    public function dataAppointment()
    {
        try {

            $appointmentData = Appointment::with(
                'callIn',
                'callIn.patient',
                'callIn.patient.typeDocument',
                'callIn.patient.RegimenType',
                'callIn.patient.contract',
                'callIn.patient.level',
                'space'
            )->find(2);

            $cup = Cup::find($appointmentData->procedure);

            if (findingKey($appointmentData->space->id)) {


                $space = Space::with('agendamiento', 'agendamiento.company', 'agendamiento.typeAppointment', 'agendamiento.location')
                    ->find($appointmentData->space->id);
            }

            $body = [
                "id" => $appointmentData->id,
                "startDate" => Carbon::parse($space->hour_start)->format('Y-m-d H:i'),
                "endDate" => Carbon::parse($space->hour_end)->format('Y-m-d H:i'),
                "state" => "Asignado",
                "type" =>      $space->agendamiento->typeAppointment->description,
                "text" => $appointmentData->observation,
                "TelehealdthUrl" => $appointmentData->link,
                "ConfirmationUrl" => "",
                "appointmentId" => $appointmentData->code,
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
                    'id' =>  $space->person->id,
                    'name' => $space->person->full_name
                ],
                'agreement' => [
                    'id' => $appointmentData->callIn->patient->contract->number,
                    'name' => $appointmentData->callIn->patient->contract->name
                ],
                'location' => [
                    'id' =>  findingKey($space->agendamiento->location) ? $space->agendamiento->location->id : null,
                    'name' => findingKey($space->agendamiento->location) ? $space->agendamiento->location->name : null
                ],
                'company' => findingKey($space->agendamiento->company) ? $space->agendamiento->company : null,
            ];

            dd($body);
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e);
        }
    }


    public function getIp()
    {

        // This creates the Reader object, which should be reused across
        // lookups.

        $reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');

        // Replace "city" with the appropriate method for your database, e.g.,
        // "country".
        $record = $reader->city('128.101.101.101');

        print($record->country->isoCode . "\n"); // 'US'
        print($record->country->name . "\n"); // 'United States'
        print($record->country->names['zh-CN'] . "\n"); // 'Estados Unidos'

        print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
        print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'

        print($record->city->name . "\n"); // 'Minneapolis'

        print($record->postal->code . "\n"); // '55455'

        print($record->location->latitude . "\n"); // 44.9733
        print($record->location->longitude . "\n"); // -93.2323

    }

    public function getwailist()
    {


        return response(WaitingList::with('appointment', 'appointment.callin')->find(304));
    }

    public function upload()
    {
        $body =  [
            "id" => 0,
            "startDate" => "2021-08-13 15:00",
            "endDate" => "2021-08-13 15:20",
            "state" => "Asignado",
            "type" => 4,
            "text" => "GESTOR : DANIELA MU\\u00d1OZ, PACIENTE ACEPTA CONSULTA DE EN LA MODALIDAD DE TELEMEDICINA SE CONFIRMAN DATOS Y NUMEROS DE TELEFONO. PACIENTE DE LA REGIONAL DE CUCUTA",
            "ConfirmationUrl" => "",
            "appointmentId" => "HEM2108130033622",
            "patient" => [
                "id" => 13494905,
                "identificationType" => "CC",
                "firstName" => "NOE",
                "secondName" => null,
                "firstlastName" => "RUEDA",
                "secondlastName" => "CORREDOR",
                "email" => "gracontreras017@gmail.com",
                "phone" => 3125944379,
                "birthDate" => "1969-01-25",
                "gender" => "M",
                "codeRegime" => "2",
                "categoryRegime" => "A",
                "codeCity" => "553",
                "codeState" => "54"
            ],
            "service" => [
                "id" => "890343",
                "name" => "CONSULTA DE CONTROL O DE SEGUIMIENTO POR ESPECIALISTA EN DOLOR Y CUIDADOS PALIATIVOS",
                "recomendations" => null
            ],
            "doctor" => [
                "id" => "9528985",
                "name" => "JOSE VICENTE  ORDUZ CAMACHO"
            ],
            "agreement" => [
                "id" => "20",
                "name" => "ECOOPSOS MEGSALUD NORTE DE SANTANDER"
            ],
            "location" =>
            ["id" => 1, "name" => "SEDE LA 127"]
        ];

        $response = Http::post(
            "https://mogarsalud.globho.com/api/integration/appointment?api_key=c09283f7-b597-4456-aaf7-d2805a4833a0",
            $body
        );

        dd($response->json());
    }

    public function uploadMassive()
    {
        dd('cxvx');

        // foreach (DB::table('appointments')->whereNull('on_globo')->orderBy('id', 'Desc' )->get() as $temp) {

        $appointment = Appointment::with('space', 'callin')->find(69279);
        // $appointment = Appointment::with('space', 'callin')->find($temp->id);
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

                    $body = [
                        "id" => 0,
                        "startDate" => Carbon::parse($appointment->space->hour_start)->format('Y-m-d H:i'),
                        "endDate" => Carbon::parse($appointment->space->hour_end)->format('Y-m-d H:i'),
                        "state" => $appointment->state,
                        "type" => ($appointment->space->agendamiento->typeAppointment->description == 'TELEMEDICINA') ? 4 : 1,
                        "text" => $appointment->observation,
                        "TelehealdthUrl" => 'https://meet.jit.si/' . $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT),
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
                        'id' =>  $this->space->person->id,
                        'name' => $this->space->person->full_name,
                        'company' => [
                        'id' => ($this->space->person->company) ? $this->space->person->company->tin : '',
                        'name' => ($this->space->person->company) ? $this->space->person->company->name : ''
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

                    $response = Http::post(
                        'https://mogarsalud.globho.com/api/integration/appointment' . "?api_key=$company->code",
                        $body
                    );

                    if ($response->ok()) {
                        $appointment->on_globo = 1;
                        $appointment->globo_id =  $response->json()['id'];
                        $appointment->save();
                        echo json_encode($response->json());
                    } else {
                        echo json_encode($response->json());
                    }
                    echo '<br>';
                }
            }


            echo 'No company ' . $appointment->id;
        } else {

            echo 'Sin spaces  ' .  $appointment->id;
        }

        echo ("=============================<br>");

        // }
    }


    public function test()
    {
       try {

         repeat:


       // $ids = ['85665','85666','85667','85669','85670','85671','85672','85673','85674','85719','85720','85721','85722','85723','85724','85725','85726','85728','85741','85742','85743','85744','85745','85746','85747','85748','85749','85778','85779','85780','85781','85782','85783','85784','85785','85786','85830','85831','85832','85833','85834','85835','85836','85837','85838','85885','85886','85887','85888','85889','85890','85891','85892','85893','85902','85903','85904','85905','85906','85907','85908','85909','85910','85936','85937','85938','85939','85940','85941','85942','85943','85944'];

        //foreach ($ids as $temp) {
        // foreach (DB::table('appointments')->whereNull('on_globo')->orderBy('id', 'Desc' )->get() as $temp) {

        //foreach ($ids as $temp) {

         $appointment = Appointment::with('space', 'callin')->find(151226);

        //$appointment = Appointment::with('space', 'callin')->find($temp);
        // $appointment = Appointment::with('space', 'callin')->find($temp->id);

        if ($appointment->space && $appointment->callin->patient) {

            $cup = Cup::find($appointment->procedure);

            if(!$cup){
                dd($appointment);
            }

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

                    $body = [
                        "id" => 752494,
                        "startDate" => Carbon::parse($appointment->space->hour_start)->format('Y-m-d H:i'),
                        "endDate" => Carbon::parse($appointment->space->hour_end)->format('Y-m-d H:i'),
                        "state" => 'Confirmado',
                        "type" => ($appointment->space->agendamiento->typeAppointment->description == 'TELEMEDICINA') ? 4 : 1,
                        "text" => $appointment->observation,
                        "TelehealdthUrl" => 'https://meet.jit.si/' . $company->simbol . date("ymd", strtotime($appointment->space->hour_start)) . str_pad($appointment->id, 7, "0", STR_PAD_LEFT),
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
                                            //   dd($body);
                                            //  dd($appointment->globo_id );


                                            // $response = Http::withOptions([
                                            //     'debug' => true,
                                            // ])->get('https://google.com');

                    $response = Http::withOptions([
                                                'verify' => false,
                                            ])
                        ->put(
                        'http://mogarsalud.globho.com/api/integration/appointment/752494'. "?api_key=$company->code",
                        // 'https://mogarsalud.globho.com/api/integration/appointment' . "?api_key=$company->code",
                        $body
                    );

                    if ($response->ok()) {
                        $appointment->on_globo = 1;
                        $appointment->globo_id =  $response->json()['id'];
                        $appointment->save();
                        echo  "Migrado..." . json_encode($response->json());
                    } else {
                        echo  "No Migrado..." . json_encode($response->json());
                    }

                    echo '<br>' .'doctor'. $appointment->space->person->full_name . 'paciente' . $appointment->callin->patient->identifier ;
                }
            }
            else{
                echo 'Sin company  ' .  $appointment->id .'doctor'. $appointment->space->person->full_name . 'paciente' . $appointment->callin->patient->identifier ;
            }

        } else {

            echo 'Sin spaces  ' .  $appointment->id ;
        }

        echo ("=============================<br>");

        }

      //}

       catch(Exception $e){
           echo ("=============================<br>");
           echo $e->getMessage();
           goto repeat;
       }
    }

    function getAppointmentByPatient() {

        $identifier = request()->get('identifier');

        $data = DB::select("SELECT spaces.hour_start as 'Hora inicio',  Concat_ws(' ', people.first_name, people.first_surname, ' ', people.id ) As Doctor,

                        Concat_ws(' ', patients.firstname, patients.surname , ' ', patients.identifier ) As Paciente,

                        specialities.name as 'Especialidad',


                        appointments.created_at as 'Creada en',

                        cups.description as 'Servicio-Cup',


                        appointments.globo_id as 'Globho id',


                        spaces.id as 'SpaceID',

                        appointments.id as 'AppoinmentID',

                        appointments.state as 'Estado',

                        companies.name as 'Company del doctor',

                        cp.name as 'Company del Paciente',

                        agendamientos.id as 'Agendamiento'

                        FROM `appointments`

                        INNER JOIN spaces on spaces.id = appointments.space_id

                        INNER JOIN cups on cups.id = appointments.procedure

                        INNER JOIN agendamientos on agendamientos.id = spaces.agendamiento_id

                        INNER JOIN specialities on specialities.id = agendamientos.speciality_id

                        INNER JOIN people on people.id = agendamientos.person_id

                        INNER JOIN companies  on companies.id = people.company_id

                        INNER JOIN call_ins on call_ins.id = appointments.call_id

                        INNER JOIN patients on patients.identifier = call_ins.Identificacion_Paciente

                        INNER JOIN companies as cp on cp.id = patients.company_id

                        WHERE patients.identifier IN ($identifier)

                        ORDER BY `appointments`.`observation` ASC");

                        return response()->json($data);

    }

    public function getPass()
    {
        return Hash::make('1000130469');
    }
}
