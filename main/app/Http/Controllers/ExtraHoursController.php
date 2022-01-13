<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ExtraHourReport;
use App\Services\ExtraHoursService;
use App\Services\LateArrivalService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExtraHoursController extends Controller
{
	use ApiResponser;
	public function __construct(ExtraHoursService $extrasService)
	{
		$this->extrasService = $extrasService;
	}

	//
	public function getDataRotative($fechaInicio, $fechaFin, $tipoTurno)
	{
		$dates = [$fechaInicio, $fechaFin];

		$companies = Company::when(Request()->get('company_id'), function ($q, $fill) {
			$q->where('id', $fill);
		})->get();

		foreach ($companies as $keyG => &$company) {

			$groups = DB::table("groups")
				->when(Request()->get('group_id'), function ($q, $fill) {
					$q->where('id', $fill);
				})->get(["id", "name"]);

			$groupsGlob = [];
			foreach ($groups as $key => &$group) {


				$group->dependencies = DB::table('dependencies')
					->when(Request()->get('dependency_id'), function ($q, $fill) {
						$q->where('id', $fill);
					})
					->where('group_id', $group->id)->get();

				if ($group->dependencies->isEmpty()) {
					unset($groups[$key]);
					continue;
				}

				foreach ($group->dependencies as $key2 =>  &$dependency) {

					$dependency->people =  $this->extrasService->getPeople($dependency->id, $tipoTurno, $company->id, $dates);
					if ($dependency->people->isEmpty()) {
						unset($group->dependencies[$key2]);
						continue;
					}
				}
				if ($group->dependencies->isEmpty()) {
					unset($groups[$key]);
					continue;
				}
				$groupsGlob[] = $group;
			}
			if ($groups->isEmpty()) {
				unset($companies[$keyG]);
				continue;
			}
			$company->groups = $groupsGlob;
		}
		return $this->success($companies);
	}


	public function getInfoTotal()
	{
		try {
			//code...

			$fechaInicio = request()->get('pd');
			$fechaFin = request()->get('ud');

			$filtroDiarioFecha = function ($query) use ($fechaInicio, $fechaFin) {
				$query->whereBetween('date', [$fechaInicio, $fechaFin])->orderBy('date');
			};


			//dd(request()->get('tipo' ));
			switch (request()->get('tipo')) {
				case 'Fijo':
					$funcionario =  $this->extrasService->funcionarioFijo($filtroDiarioFecha);
					return $this->success($this->extrasService->calcularExtras($funcionario));
					break;
				case 'Rotativo':


					$funcionario =   $this->extrasService->funcionarioRotativo($filtroDiarioFecha);
					return $this->success($this->extrasService->calcularExtras($funcionario));

					break;
				default:
					return abort(404);
					break;
			}
		} catch (\Throwable $th) {
			//throw $th;
			return $th->getMessage() . ' msg: ' . $th->getLine() . ' ' . $th->getFile();
		}
	}

	public function store()
	{
		try {
			//code...
			$atributos = request()->validate([
				'person_id' => 'required',
				'date' => 'required',
				'ht' => 'required',
				'hed' => 'required',
				'hen' => 'required',
				'hedfd' => 'required',
				'hedfn' => 'required',
				'rn' => 'required',
				'rf' => 'required',
				'hed_reales' => 'required',
				'hen_reales' => 'required',
				'hedfd_reales' => 'required',
				'hedfn_reales' => 'required',
				'rn_reales' => 'required',
				'rf_reales' => 'required',
			]);

			ExtraHourReport::create($atributos);
			return response()->json(['message' => 'Horas extras validadas correctamente']);
		} catch (\Throwable $th) {
			//throw $th;
			return response($th->getMessage());
		}
	}
	public function update($id, Request $request)
	{
		try {
			$validada = ExtraHourReport::findOrFail($id);

			//code...
			$atributos = request()->validate([
				'person_id' => 'required',
				'date' => 'required',
				'ht' => 'required',
				'hed' => 'required',
				'hen' => 'required',
				'hedfd' => 'required',
				'hedfn' => 'required',
				'rn' => 'required',
				'rf' => 'required',
				'hed_reales' => 'required',
				'hen_reales' => 'required',
				'hedfd_reales' => 'required',
				'hedfn_reales' => 'required',
				'rn_reales' => 'required',
				'rf_reales' => 'required',
			]);

			$validada->update($atributos);
			return response()->json(['message' => 'Horas extras validadas correctamente']);
		} catch (\Throwable $th) {
			//throw $th;
			return response($th->getMessage());
		}
	}

	public function getDataValid($person_id, $fecha)
	{
		$validada = ExtraHourReport::where('date', $fecha)->where('person_id', $person_id)->orderBy('date')->first();
		return $this->success($validada);
	}
}
