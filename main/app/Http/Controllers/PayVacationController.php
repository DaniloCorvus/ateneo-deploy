<?php

namespace App\Http\Controllers;

use App\Models\PayVacation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayVacationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            DB::table('payroll_factors as pf')
            ->select(
                'pf.id',
                'pf.disability_leave_id',
                'pf.person_id',
                'pf.date_start',
                'pf.date_end',
                DB::raw('concat(p.first_name," ",p.first_surname) as name'),
                'p.image',
                'w.salary',
                'depe.name as dependency',
                // 'pv.state'
            )
            // ->join('pay_vacations as pv', 'pv.payroll_factor_id', '=', 'pf.id')
            ->join('people as p', 'p.id', '=', 'pf.person_id')
            ->join('disability_leaves as d', 'd.id', '=', 'pf.disability_leave_id')
            ->join('work_contracts as w', 'w.person_id', '=', 'p.id')
            ->join('positions as posi', 'posi.id', '=', 'w.position_id')
            ->join('dependencies as depe', 'depe.id', '=', 'posi.dependency_id')
            ->where('d.concept', '=', 'Vacaciones')
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
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
        return $this->success(
            PayVacation::create($request->all())
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
