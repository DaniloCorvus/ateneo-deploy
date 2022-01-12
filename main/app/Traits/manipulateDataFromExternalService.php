<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait manipulateDataFromExternalService
{
    public function getDateOfBirth($porciones)
    {
        switch (count($porciones)) {
            case '6':
                return  Carbon::now()->subYears($porciones[0])->subMonths($porciones[2])->subDays($porciones[4])->format('Y-m-d');
                break;
            case '5':
            case '4':
                return  Carbon::now()->subYears($porciones[0])->subMonths($porciones[2])->format('Y-m-d');
                break;
            default:
                return "0000-00-00";
                break;
        }
        Carbon::now()->subYears($porciones[0])->subMonths($porciones[2])->subDays($porciones[4])->format('Y-m-d');
    }

    public function appendMunicipaly($cityName)
    {
        return  DB::table('municipalities')->where('name', normalize($cityName))->first();
    }

    public function appendDeparment($cityName)
    {
        $cities = DB::select('select name, department_id from municipalities');
        foreach ($cities as  $city) {
            if (normalize($city->name) == normalize($cityName)) {
                return DB::table('departments')->find($city->department_id);
            }
        }
    }

    public function appendRegional($dptoId)
    {
        return DB::table('department_regional')->where('departamento_id', $dptoId)
            ->join('regionals', 'regionals.id', '=', 'department_regional.regional_id')->value('regionals.id');
    }
}
