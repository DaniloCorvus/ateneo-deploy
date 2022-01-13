<?php

namespace App\Services;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class RotatingHourService
{
	/**Funciones de estadisticas */
	public static function getPeople($id, $company_id)
	{
		$compare = Request()->get('turn_type') == 'Rotativo' ? 'rotating_turn_diaries' : 'fixed_turn_diaries';
		return DB::table("people as p")
			->join("work_contracts as w", function ($join) {
				$join->on(
					"p.id",
					"=",
					"w.person_id"
				)->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                        join people as u2 on u2.id = a2.person_id group by u2.id)');
			})
			->join("positions as ps", "ps.id", "=", "w.position_id")
			->where("ps.dependency_id", $id)
			->where("w.turn_type", 'Rotativo')
			->where("w.company_id", $company_id)
			->when( Request()->get('person') , function($q, $fill)
            {
                $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
            })
			->select("p.first_name", "p.first_surname", "p.id", "p.image")
			->get()->toArray();
	}
	public static function getHours($personId)
	{
		return DB::table("rotating_turn_hours")
			->select("*")
			->where("person_id", $personId)
			->get();
	}


	//Consulta Turnos rotativos

	
}
