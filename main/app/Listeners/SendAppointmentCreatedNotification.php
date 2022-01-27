<?php

namespace App\Listeners;

use App\Events\AppointmentModify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotifyMail;
use App\Models\CallIn;
use App\Models\Patient;
use App\Models\Cup;
use App\Models\Location;
use App\Models\Contract;
use App\Models\TypeDocument;
use App\Models\Level;
use App\Models\Municipality;
use App\Models\Department;
use App\Models\RegimenType;
use Carbon\Carbon;

class SendAppointmentCreatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 6539f628b4ae1684293127dbd8b7c218-0ff63c3a-b216-4d9b-a25a-ffd99b3058e3
    // AteneoSms
    /**
     * Handle the event.
     *
     * @param  AppointmentModify  $event
     * @return void
     */
    public function handleMail($appointment, $space,  $data, $another)
    {
       	$cup = Cup::find($data['procedureId']['value']);
		$location = in_array('sede', $another) ? Location::find($another['sede']) : Location::find($data['patient']['location_id']);
		$contract = Contract::find($data['patient']['contract_id']);
		$typeDocument =	TypeDocument::find($data['patient']['type_document_id']);
		$regimenType =	RegimenType::find($data['patient']['regimen_id']);
		$level = Level::find($data['patient']['level_id']);
		$municipality = Municipality::find($data['patient']['municipality_id']);
		$department = Department::find($data['patient']['department_id']);
		$agendamiento = $space->with('agendamiento', 'agendamiento.company', 'agendamiento.typeAppointment', 'agendamiento.location' )->find($space->id);

		$body = [
			"id" => $appointment->id,
			"startDate" => Carbon::parse($space->hour_start)->format('Y-m-d H:i'),
			"endDate" => Carbon::parse($space->hour_end)->format('Y-m-d H:i'),
			"state" => "Asignado",
			"type" =>  	$space->agendamiento->typeAppointment->description,
			"text" => $appointment->observation,
			"TelehealdthUrl" => $appointment->link,
			"ConfirmationUrl" => "",
			"appointmentId" => $appointment->code,
			"patient" => [
				"id" => $data['patient']['identifier'],
				"identificationType" => $typeDocument->code,
				"firstName" => $data['patient']['firstname'],
				"secondName" =>  $data['patient']['middlename'],
				"firstlastName" => $data['patient']['surname'],
				"secondlastName" => $data['patient']['secondsurname'],
				"email" => $data['patient']['email'],
				"phone" => $data['patient']['phone'],
				"birthDate" => $data['patient']['date_of_birth'],
				"gender" =>  $data['patient']['gener'],
				"codeRegime" => $regimenType->code,
				"categoryRegime" => $level->code,
				"codeCity" => $municipality->code,
				"codeState" => $department->code,
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
				'id' => $contract->number,
				'name' => $contract->name
			],
			'location' => [
				'id' => $location->id,
				'name' => $location->name
			],
			'location_for_appointment' => [
				'name' =>   ($space->agendamiento->location) ? $space->agendamiento->location->name : '',
				'address' =>  ($space->agendamiento->location) ? $space->agendamiento->location->address : ''
			],
			
			'company' => $space->agendamiento->company
		];
            
        try {
                
			if (isset($space)) {
				Mail::to($body['patient']['email'])->send(new NotifyMail($body));
			}
			
		} catch (\Throwable $th) {
			Log::warning(" :boom: Correo no enviado!  " . $body['patient']['email'] );
		}
		
        
    }
    
    public function handle()
    {
       
    }
}
