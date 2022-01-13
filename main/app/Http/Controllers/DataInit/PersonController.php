<?php

namespace App\Http\Controllers\DataInit;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Paciente;
use App\Models\Patient;
use App\Services\EcoopsosService;
use App\Services\MedimasService;
use App\Services\MedimasServiceSubsidiado;

use App\Services\PersonService;
use App\Services\SpecialitysDoctorsService;
use App\Traits\ApiResponser;
use App\Traits\HandlerContructTablePerson;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PersonController extends Controller
{
    use ApiResponser, HandlerContructTablePerson;

    public  $medimasService;
    public  $ecoopsosService;
    public  $patient;
    public  $message;
    public $specialitysDoctorsService;

    public function __construct(PersonService $personService, SpecialitysDoctorsService $specialitysDoctorsService)
    {
        $this->personService = $personService;
        $this->plainData = [];
        $this->specialitysDoctorsService = $specialitysDoctorsService;
        $this->documentypes  = collect([]);
    }

    public function CrucePacientes()
    {
        ini_set('max_execution_time', 0);
        DB::table('Paciente-Cruce-Augusto')
            ->where('Paciente-Cruce-Augusto.Actualizado', '=', 0)
            ->orderBy('Paciente-Cruce-Augusto.id')->chunk(1, function ($pacientes) {
                $i = 0;

                foreach ($pacientes as $paciente) {
                    $i++;
                    echo $paciente->Identificacion;

                    $dataPatient = $this->searchPatientInServicesUpdate($paciente->Tipo_Identificacion, $paciente->Identificacion);

                    if (isset($dataPatient['firstname'])) {
                        $update = [
                            'Tipo_Identificacion_Real' => $dataPatient['type_document_id'],
                            'Primer_Nombre_Real' => $dataPatient['firstname'],
                            'Segundo_Nombre_Real' => $dataPatient['middlename'],
                            'Primer_Apellido_Real' => $dataPatient['surname'],
                            'Segundo_Apellido_Real' => $dataPatient['secondsurname'],
                            'EPS_Real' => $dataPatient['database'],
                            'Regimen_Real' => $dataPatient['regimen_id'],
                            'Estado_Real' => $dataPatient['state'],
                            'Actualizado' => 1
                        ];

                        DB::table('Paciente-Cruce-Augusto')
                            ->where('Identificacion', $paciente->Identificacion)
                            ->update($update);

                        echo " - Actualizado<br>";
                    } else {
                        DB::table('Paciente-Cruce-Augusto')
                            ->where('Identificacion', $paciente->Identificacion)
                            ->update(['Actualizado' => 2]);
                        echo " - No Encontrado<br>";
                    }
                }
            });
    }
    public function get()
    {
        try {
            $this->persons = json_decode($this->personService->get(), true);
            handlerTableCreate($this->persons);
            return $this->success('Tabla creada Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function store()
    {
        try {

            $this->persons = json_decode($this->personService->get(), true);
            $this->handlerInsertTable($this->persons);
            return $this->success('Datos insertados Correctamente');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function customUpdate()
    {

        $documentType =  request()->get('tipo_documento');
        $documentNumber =  request()->get('identificacion');

        if ($this->createOrUpdatePatient($documentType, $documentNumber)) {
            return response(['message' => $this->message, 'data' => $this->patient]);
        }
        return $this->success(['EnBase' => 'No', 'paciente' => 'PACIENTE NO REGISTRADO EN BASE DE DATOS']);
    }

    public function customUpdateOld($identificacion, $tipo_documento)
    {
        
        $documentType =  $tipo_documento;
        $documentNumber =  $identificacion;

        // dd([$documentType, $documentNumber]);

        // Log::info([$documentType, $documentNumber]);

        if ($this->createOrUpdatePatient($documentType, $documentNumber)) {
            return $this->success(['EnBase' => $this->message, 'paciente' => $this->patient]);
        }
        return $this->success(['EnBase' => 'No', 'paciente' => 'PACIENTE NO REGISTRADO EN BASE DE DATOS']);
    }

    public function validatePatientByLineFront()
    {
        $documentType =  request()->get('type_document');
        $documentNumber =  request()->get('identifier');

        if ($this->createOrUpdatePatient($documentType, $documentNumber)) {
            return $this->success($this->plainData);
        }
        return $this->success(['EnBase' => 'No', 'paciente' => 'PACIENTE NO REGISTRADO EN BASE DE DATOS']);
    }


    public function createOrUpdatePatient($documentType, $documentNumber)
    {

        $dataPatient = $this->searchPatientInServices($documentType, $documentNumber);

        if (count($dataPatient) == 0) {
            return false;
        }

        $this->patient = Patient::with(
            'eps',
            'company',
            'municipality',
            'department',
            'regional',
            'level',
            'regimentype',
            'typedocument',
            'contract',
            'location'
        )->firstWhere('identifier', $dataPatient['identifier']);


        if ($this->patient) {
            $this->patient->update($dataPatient);
            $this->message = 'Si';
        } else {

            if (count($dataPatient) <= 4) {
                return false;
            }

            $this->patient = Patient::Create($dataPatient);
            $this->message = 'No';
        }

        $this->verifyPatient();
        return $this->patient;
    }

    public function verifyPatient()
    {
        $this->plainData = $this->patient;
        $this->patient = new PatientResource($this->patient);
    }

    public function searchPatientInServices($documentType, $documentNumber)
    {
        
        $this->medimasService = $dataPatient = [];

        $p = null;
        
        // $p = Patient::firstWhere('identifier', $documentNumber);

        if ($p) {
            $dataPatient['identifier'] = $documentNumber;
            return $dataPatient;
        }

        $this->medimasService =  new MedimasService($documentType, $documentNumber);
        $dataPatient = $this->medimasService->getDataMedimas()->loopDataMedimas();

        
        if (count($dataPatient) <= 4) {
            $this->medimasService =  new MedimasServiceSubsidiado($documentType, $documentNumber);
            $dataPatient =  $this->medimasService->getDataMedimas()->loopDataMedimas();
        }
        
        
        if (count($dataPatient) <= 4) {
            $this->medimasService =  new EcoopsosService($documentType, $documentNumber);
            $dataPatient =  $this->medimasService->getDataWebEcoopsos()->loopDataEcoopsos();
        }
                
        return $dataPatient;
        
    }
    public function searchPatientInServicesUpdate($documentType, $documentNumber)
    {
        $this->medimasService = $dataPatient = [];

        $this->medimasService =  new MedimasService($documentType, $documentNumber);
        $dataPatient = $this->medimasService->getDataMedimas()->loopDataMedimas();

        if (count($dataPatient) == 4 || count($dataPatient) < 4) {
            echo "----MEDIMAS SUBSIDIADO----";
            $this->medimasService =  new MedimasServiceSubsidiado($documentType, $documentNumber);
            $dataPatient =  $this->medimasService->getDataMedimas()->loopDataMedimas();
        }

        if (count($dataPatient) == 4 || count($dataPatient) < 4) {
            echo " -- PARECE DE ECOOPSOS -- ";
            $this->medimasService =  new EcoopsosService($documentType, $documentNumber);
            $dataPatient =  $this->medimasService->getDataWebEcoopsos()->loopDataEcoopsos();
        }

        return $dataPatient;
    }
}
