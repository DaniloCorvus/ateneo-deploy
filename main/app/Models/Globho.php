<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use stdClass;
use Illuminate\Support\Facades\Log;


class Globho
{

	public $body;

	public function __construct($appointment, $space,  $data, $another)
	{
		$cup = Cup::find($data['procedureId']['value']);
		$location = in_array('sede', $another) ? Location::whereHas('company', function ($q) {
			$q->where('type', true);
		})->where('id', 49)->first() : Location::find($data['patient']['location_id']);
		$contract = Contract::find($data['patient']['contract_id']);
		$typeDocument =	TypeDocument::find($data['patient']['type_document_id']);
		$regimenType =	RegimenType::find($data['patient']['regimen_id']);
		$level = Level::find($data['patient']['level_id']);
		$municipality = Municipality::find($data['patient']['municipality_id']);
		$department = Department::find($data['patient']['department_id']);


		$this->body = [
			"id" => 0,
			"startDate" => Carbon::parse($space->hour_start)->format('Y-m-d H:i'),
			"endDate" => Carbon::parse($space->hour_end)->format('Y-m-d H:i'),
			"state" => "Asignado",
			"type" => ($appointment->space->agendamiento->typeAppointment->description == 'TELEMEDICINA') ? 4 : 1,
			"text" => $appointment->observation,
			"ConfirmationUrl" => "",
			"appointmentId" => $appointment->code,
			"TelehealdthUrl" => $appointment->link,
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
				"codeCity" => substr($municipality->code, 2, 5),
				"codeState" => $department->code,
			],
			'service' => [
				'id' => $cup->code,
				'name' => $cup->description,
				'recomendations' => $cup->recomendation
			],
			'doctor' => [
				'id' =>  $space->person->identifier,
				'name' => $space->person->full_name,
				'company' => [
					'id' =>  ($space->person->company) ? $space->person->company->tin : '',
					'name' =>  ($space->person->company) ? $space->person->company->name : ''
				],
			],
			'agreement' => [
				'id' => $contract->number,
				'name' => $contract->name
			],
			'location' => [
				'id' => $location->globo_id,
				'name' => $location->name
			]

		];
	}
}
