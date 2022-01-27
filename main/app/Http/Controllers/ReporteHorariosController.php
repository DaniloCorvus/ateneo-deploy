<?php

namespace App\Http\Controllers;

use App\Exports\FixedTurnDiaryExport;
use App\Exports\RotatingTurnDiaryExport;
use App\Models\Company;
use App\Services\DiaryService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteHorariosController extends Controller
{
	use ApiResponser;
	public function fixed_turn_diaries($fechaInicio, $fechaFin)
	{
		$dates = [$fechaInicio, $fechaFin];

		$companies = Company::when(Request()->get("company_id"), function (
			$q,
			$fill
		) {
			$q->where("id", $fill);
		})->get();

		foreach ($companies as $keyG => &$company) {
			$groups = DB::table("groups")
				->when(Request()->get('group_id'), function ($q, $fill) {
					$q->where('id', $fill);
				})->get(["id", "name"]);
			$groupsGlob = [];
			foreach ($groups as $key => &$group) {
				$group->dependencies = DB::table("dependencies")
					->when(Request()->get('dependency_id'), function ($q, $fill) {
						$q->where('id', $fill);
					})
					->where("group_id", $group->id)
					->get();

				if ($group->dependencies->isEmpty()) {
					unset($groups[$key]);
					continue;
				}

				foreach ($group->dependencies as $key2 => &$dependency) {
					$dependency->people = DiaryService::getPeople(
						$dependency->id,
						$dates,
						$company->id
					);
					if ($dependency->people->isEmpty()) {
						unset($group->dependencies[$key2]);
						continue;
					}

					foreach ($dependency->people as &$person) {
						if (Request()->get('turn_type') == 'Rotativo') {
							$person->diaries = DiaryService::getDiariesRotative(
								$person->id,
								$dates
							);
						} else {
							$person->diaries = DiaryService::getDiaries(
								$person->id,
								$dates
							);
						}
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

	public function download($fechaInicio, $fechaFin)
	{
		# code...

		$dates = [$fechaInicio, $fechaFin];
		# code...


		if ( Request()->get('turn_type')  == 'Rotativo') {
			return Excel::download(new RotatingTurnDiaryExport($dates), 'users.xlsx');
		}else{
			return Excel::download(new FixedTurnDiaryExport($dates), 'users.xlsx');
        }
	}
}
