<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class CognitiveService
{
	public $ocpApimSubscriptionKey;
	public $azure_grupo;
	public $uriBase;
	public $endPoint;

	public function __construct()
	{
		$this->ocpApimSubscriptionKey = "df2f7a1cb9a14c66b11a7a2253999da5";
		$this->azure_grupo = "personalnuevo";
		$this->uriBase =
			"https://facemaqymon2021.cognitiveservices.azure.com/face/v1.0";
		$this->endPoint =  url();
	}
	/** SI LA PERSONA NO TIENE PERSON ID SE CREA EL REGISTRO EN MICROSOFT */
	public function createPerson($person)
	{
		if (
			$person->personId == "0" ||
			$person->personId == "null" ||
			$person->personId == null
		) {
			/****** */
			$parameters = [];
			$response = Http::accept("application/json")
				->withHeaders([
					"Content-Type" => "application/json",
					"Ocp-Apim-Subscription-Key" =>
					$this->ocpApimSubscriptionKey,
				])
				->post(
					$this->uriBase .
						"/persongroups/" .
						$this->azure_grupo .
						"/persons" .
						http_build_query($parameters),
					[
						"name" =>
						$person->first_name . " " . $person->first_surname,
						"userData" => $person->identifier,
					]
				);
			$res = $response->json();
			if (!isset($res["personId"])) {
				throw new Exception($res);
			}
			return $res["personId"];
		}
	}
	public function deleteFace($person)
	{
		if ($person->persistedFaceId && $person->persistedFaceId != "0") {
			$parameters = [];
			$response = Http::accept("application/json")
				->withHeaders([
					"Content-Type" => "application/json",
					"Ocp-Apim-Subscription-Key" =>
					$this->ocpApimSubscriptionKey,
				])
				->post(
					$this->uriBase .
						"/persongroups/" .
						$this->azure_grupo .
						"/persons/" .
						$person->personId .
						"/persistedFaces/" .
						$person->persistedFaceId .
						http_build_query($parameters),
					[
						"Ocp-Apim-Subscription-Key" =>
						$this->ocpApimSubscriptionKey,
					]
				);
		}
	}
	public function createFacePoints($person)
	{
		
		$parameters = [
			"detectionModel" => "detection_02",
		];
		$response = Http::accept("application/json")
			->withHeaders([
				"Content-Type" => "application/json",
				"Ocp-Apim-Subscription-Key" => $this->ocpApimSubscriptionKey,
			])
			->post(
				$this->uriBase .
					"/persongroups/" .
					$this->azure_grupo .
					"/persons/" .
					$person->personId .
					"/persistedFaces",
				[
					"url" => $person->image,
					"detectionModel" => "detection_02",
				]
			);
		$resp = $response->json();

		if (isset($resp["persistedFaceId"]) && $resp["persistedFaceId"] != "") {
			return $resp["persistedFaceId"];
		} else {
			//  if (Storage::disk('s3')->exists($img)) {
			//  Storage::disk('s3')->delete($img);
			//}
			if ($resp["error"]["code"] == "InvalidImage") {
				throw new Exception(
					"No se ha encontrado un rostro en la imagen, revise e intente nuevamente"
				);
			}
			throw new Exception('Ha ocurrido un error inisperado'.$resp["error"]["code"]);
		}
	}
	public function train()
	{
		return Http::accept("application/json")
			->withHeaders([
				"Ocp-Apim-Subscription-Key" => $this->ocpApimSubscriptionKey,
			])
			->post(
				$this->uriBase .
					"/persongroups/" .
					$this->azure_grupo .
					"/train",
				[]
			)
			->json();
	}
}
