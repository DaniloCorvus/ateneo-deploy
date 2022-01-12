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

class SendAppointmentModifiedNotification
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
    public function handleMail($appointment)
    {        
        $cup = Cup::find($appointment->procedure);

		$body = [
			"id" => $appointment->id,
			"startDate" => Carbon::parse($appointment->space->hour_start)->format('Y-m-d H:i'),
			"endDate" => Carbon::parse($appointment->space->hour_end)->format('Y-m-d H:i'),
			"state" => $appointment->state,
			"type" =>  	$appointment->space->agendamiento->typeAppointment->description,
			"text" => $appointment->observation,
			"TelehealdthUrl" => $appointment->link,
			"ConfirmationUrl" => "",
			"appointmentId" => $appointment->code,
			"patient" => [
				"id" => $appointment->callin->patient->identifier,
				"identificationType" =>  $appointment->callin->patient->code,
				"firstName" => $appointment->callin->patient->firstname,
				"secondName" =>  $appointment->callin->patient->middlename,
				"firstlastName" => $appointment->callin->patient->surname,
				"secondlastName" => $appointment->callin->patient->secondsurname,
				"email" => $appointment->callin->patient->email,
				"phone" => $appointment->callin->patient->phone,
				"birthDate" => $appointment->callin->patient->date_of_birth,
				"gender" =>  $appointment->callin->patient->gener,
				// "codeRegime" => $regimenType->code,
				// "categoryRegime" => $level->code,
				// "codeCity" => $municipality->code,
				// "codeState" => $department->code,
			],
			'service' => [
				'id' => $cup->code,
				'name' => $cup->description,
				'recomendations' => $cup->recomendation
			],
			'doctor' => [
				'id' =>  $appointment->space->person->id,
				'name' => $appointment->space->person->full_name
			],
			'agreement' => [
				// 'id' => $contract->contract_number,
				// 'name' => $contract->contract_name
			],
			'location' => [
				// 'id' => $location->id,
				// 'name' => $location->name
			],
			
			'company' => $appointment->space->agendamiento->company
		];
        
    
         if(isset($appointment->space) && isset($body['patient']['email'])){
            Mail::to($body['patient']['email'])->send(new NotifyMail($body));
         }
        
    }
    
    public function handle()
    {
       
    }
}
