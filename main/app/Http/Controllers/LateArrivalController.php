<?php

namespace App\Http\Controllers;

use App\Exports\LateArrivalExport;
use App\Models\Company;
use App\Models\Group;
use App\Services\LateArrivalService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LateArrivalController extends Controller
{
    use ApiResponser;


    //
    public function getData($fechaInicio, $fechaFin)
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

                    $dependency->people = LateArrivalService::getPeople($dependency->id, $dates, $company->id);
                    if ($dependency->people->isEmpty()) {
                        unset($group->dependencies[$key2]);
                        continue;
                    }

                    foreach ($dependency->people as &$person) {
                        $person->late_arrivals = LateArrivalService::getLastArrivals($person->id, $dates);
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



    public  function statistics($fechaInicio, $fechaFin, Request $request)
    {
        $dates = [$fechaInicio, $fechaFin];
        $res = [];

        if ($request->get('type') == 'diary') {
            $lates = LateArrivalService::getLates($dates);
            $res['lates'] = $lates;

            $res['allByDependency'] = LateArrivalService::getAllByDependecies($dates);
            $allMarcations = LateArrivalService::allMarcations($dates);

            $res['percentage'] = round($allMarcations ? ($lates->total * 100) / $allMarcations : 0);
            return $this->success($res);
        }

        $res['lates'] =  LateArrivalService::getAllLinear($dates);
        return $this->success($res);
    }

    public function download($fechaInicio, $fechaFin,Request $req)
    {
        $dates = [$fechaInicio,$fechaFin];
        # code...
        return Excel::download(new LateArrivalExport($dates), 'users.xlsx');
        return 'asd';
    }
}
