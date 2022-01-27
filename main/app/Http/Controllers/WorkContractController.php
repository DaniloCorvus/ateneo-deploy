<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\WorkContract;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 20;
        return $this->success(
            DB::table('people as p')
                ->select(
                    DB::raw('concat("CON", w.id) as code'),
                    'p.id',
                    'p.identifier',
                    'p.first_name',
                    'p.first_surname',
                    'p.second_name',
                    'p.second_surname',
                    'p.image',
                    'p.status',
                    'posi.name as position',
                    'depe.name as dependency_name',
                    'gr.name as group_name',
                    'co.name as company_name',
                    'w.date_of_admission',
                    'w.date_end',
                    'wt.name as work_contract_type',
                    'wt.conclude'
                )
                ->when(Request()->get('person'), function ($q, $fill) {
                    $q->where(DB::raw('concat(p.first_name, " ",p.first_surname)'), 'like', '%' . $fill . '%');
                })
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('work_contract_types as wt', 'wt.id', 'w.work_contract_type_id')
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as depe', function ($join) {
                    $join->on('depe.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'group_id');
                })
                ->when(Request()->get('position'), function ($q, $fill) {
                    $q->where('posi.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('dependency'), function ($q, $fill) {
                    $q->where('depe.id', 'like', '%' . $fill . '%');
                })

                ->when(Request()->get('group'), function ($q, $fill) {
                    $q->where('gr.id', 'like', '%' . $fill . '%');
                })
                ->join('companies as co', function ($join) {
                    $join->on('co.id', '=', 'w.company_id');
                })
                ->when(Request()->get('company'), function ($q, $fill) {
                    $q->where('co.id', 'like', '%' . $fill . '%');
                })
                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }


    public function contractsToExpire()
    {
        $data = Request()->all();
        $page = key_exists('page', $data) ? $data['page'] : 1;
        $pageSize = key_exists('pageSize', $data) ? $data['pageSize'] : 2;
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname',
                    'p.image',
                    'w.date_of_admission',
                    'w.date_end'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereNotNull('date_end')->whereBetween('date_end', [Carbon::now()->addDays(30), Carbon::now()->addDays(45)])->orderBy('name', 'Desc');
                })
                ->paginate($pageSize, ['*'], 'page', $page)
        );
    }

   public function getPreliquidated()
    {
        $people = DB::table('people as p')
        ->select(
            'p.id',
            'p.first_name',
            'p.second_name',
            'p.first_surname',
            'p.second_surname',
            'p.image',
            'posi.name',
            'p.updated_at',
            'posi.name as position'
            )
            ->join('work_contracts as w', function ($join) {
                $join->on('w.person_id', '=', 'p.id');
            })
            ->join('positions as posi', function ($join) {
                $join->on('posi.id', '=', 'w.position_id');
            })
            ->where('status', 'PreLiquidado')
            ->get();
            /* for ($i = 0; $i < count($people); $i++) { 
                $fecha = $people[$i]->updated_at;
                // dd($fecha);
                // $fechas = Carbon::parse()->locale('es')->diffForHumans();
            } */
            return $this->success($people);
    }

    public function getLiquidated($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id',
                    'p.first_name',
                    'p.second_name',
                    'p.first_surname',
                    'p.second_surname'
                )
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function getTrialPeriod()
    {
        // $contratoIndeDefinido = DB::table('people as p')
        //     ->select(
        //         'p.image',
        //         'w.id',
        //         'w.date_of_admission',
        //         DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
        //         DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 60 DAY),"%m-%d") as dates'),
        //         DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days'),
        //     )
        //     ->where('wt.conclude', '=', 0)
        //     ->where('p.status', '=', 'Activo')
        //     ->join('work_contracts as w', function ($join) {
        //         $join->on('w.person_id', '=', 'p.id')
        //             ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
        //     join people as u2 on u2.id = a2.person_id group by u2.id)');
        //     })
        //     ->join('work_contract_types as wt', function ($join) {
        //         $join->on('wt.id', '=', 'w.work_contract_type_id');
        //     })
        //     ->havingBetween('worked_days', [40, 60]);

        // $contratoFijo = DB::table('people as p')
        //     ->select(
        //         'p.image',
        //         'w.id',
        //         'w.date_of_admission',
        //         DB::raw('concat(p.first_name, " ",p.first_surname) as names'),
        //         DB::raw('DATE_FORMAT(DATE_ADD(w.date_of_admission, INTERVAL 36 DAY),"%m-%d") as dates'),
        //         DB::raw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) AS worked_days')
        //     )
        //     ->where('wt.conclude', '=', 1)
        //     ->where('p.status', '=', 'Activo')
        //     ->join('work_contracts as w', function ($join) {
        //         $join->on('w.person_id', '=', 'p.id')
        //             ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
        //     join people as u2 on u2.id = a2.person_id group by u2.id)');
        //     })
        //     ->join('work_contract_types as wt', function ($join) {
        //         $join->on('wt.id', '=', 'w.work_contract_type_id');
        //     })
        //     ->whereRaw('TIMESTAMPDIFF(DAY, w.date_of_admission, CURDATE()) <=TRUNCATE( (TIMESTAMPDIFF (DAY, w.date_of_admission, w.date_end) / 5),0)')
        //     ->union($contratoIndeDefinido)
        //     ->get();
        // return $this->success(
        //     $contratoFijo
        // );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    use ApiResponser;

    public function show($id)
    {
        return $this->success(
            DB::table('people as p')
                ->select(
                    'p.id as person_id',
                    'posi.name as position_name',
                    'd.name as dependency_name',
                    'gr.name as group_name',
                    'd.group_id',
                    'w.rotating_turn_id',
                    'posi.dependency_id',
                    'f.name as fixed_turn_name',
                    'c.name as company_name',
                    'w.turn_type',
                    'w.position_id',
                    'w.company_id',
                    'w.fixed_turn_id',
                    'w.id'
                )
                ->join('work_contracts as w', function ($join) {
                    $join->on('w.person_id', '=', 'p.id')
                        ->whereRaw('w.id IN (select MAX(a2.id) from work_contracts as a2
                            join people as u2 on u2.id = a2.person_id group by u2.id)');
                })
                ->join('positions as posi', function ($join) {
                    $join->on('posi.id', '=', 'w.position_id');
                })
                ->join('dependencies as d', function ($join) {
                    $join->on('d.id', '=', 'posi.dependency_id');
                })
                ->join('groups as gr', function ($join) {
                    $join->on('gr.id', '=', 'd.group_id');
                })
                ->leftJoin('fixed_turns as f', function ($join) {
                    $join->on('f.id', '=', 'w.fixed_turn_id');
                })
                ->join('companies as c', function ($join) {
                    $join->on('c.id', '=', 'w.company_id');
                })
                ->where('p.id', '=', $id)
                ->first()
        );
    }

    public function updateEnterpriseData(Request $request)
    {
        try {
            $work_contract = WorkContract::find($request->get('id'));
            $work_contract->update($request->all());
            return response()->json(['message' => 'Se ha actualizado con éxito']);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
