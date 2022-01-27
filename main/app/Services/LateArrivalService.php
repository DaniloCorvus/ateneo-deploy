<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LateArrivalService
{
    /**Funciones de estadisticas */
    static public function getLates($dates)
    {
        return  DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                        join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('p.id',$fill);
            })
            ->selectRaw('count(*) as total')
            ->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(l.real_entry) - TIME_TO_SEC(l.entry))) AS time_diff_total')
            ->first();
    }

    static public function getAllByDependecies($dates)
    {
       return DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                                join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->join('dependencies as d', 'd.id', '=', 'ps.dependency_id')
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('p.id',$fill);
            })
            ->selectRaw('count(*) as total, d.name')
            ->groupBy('d.id')
            ->get();
    }

    static public function allMarcations($dates)
    {
        return DB::table('marcations as m')
            ->join('people as p', 'm.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                    join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('p.id',$fill);
            })
            ->whereBetween(DB::raw('DATE(date)'), $dates)
            ->count();
    }

    static public function getAllLinear($dates)
    {
        return DB::table('late_arrivals as l')
            ->join('people as p', 'l.person_id', '=', 'p.id')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->when(Request()->get('company_id'), function ($q, $fill) {
                $q->where('w.company_id', $fill);
            })
            ->whereBetween(DB::raw('DATE(l.created_at)'), $dates)
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('p.id',$fill);
            })
            ->selectRaw('count(*) as total')
            ->selectRaw('DAY(l.created_at) as day ')
            ->groupBy(DB::raw('DATE(l.created_at) '))
            ->get();
    }

    /**End Funciones de estadisticas */


    /**Arrivals */

    static public function getLastArrivals($personId, $dates)
    {
        return DB::table('late_arrivals as la')
            ->select('*')
            ->selectRaw('TIMEDIFF(la.real_entry,la.entry) AS entry_diff')
            ->where('la.person_id', $personId)
            ->whereBetween(DB::raw('DATE(la.created_at)'), $dates)
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('la.person_id',$fill);
            })
            ->get();
    }

    static public function getPeople($id, $dates, $company_id)
    {
        return DB::table('people as p')
            ->join('work_contracts as w', function ($join) {
                $join->on('p.id', '=', 'w.person_id')
                    ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                        join people as u2 on u2.id = a2.person_id group by u2.id)');
            })
            ->join('positions as ps', 'ps.id', '=', 'w.position_id')
            ->where('ps.dependency_id', $id)
            ->where('w.company_id', $company_id)
            ->whereExists(function ($query) use ($dates) {
                $query->select(DB::raw(1))
                    ->from('late_arrivals as la')
                    ->whereColumn('la.person_id', 'p.id')
                    ->whereBetween(DB::raw('DATE(la.created_at)'), $dates);
            })
            ->when(Request()->get('person_id'),function($q,$fill){
                $q->where('p.id',$fill);
            })
            ->select('p.first_name', 'p.first_surname', 'p.id','p.image')
            ->get();
    }

}
