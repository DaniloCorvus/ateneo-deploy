<?php

namespace App\Http\Controllers;

use App\Models\FixedAssetType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixedAssetTypeController extends Controller
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
            FixedAssetType::all(['name as text', 'id as value'])
        );
    }

    public function paginate()
    {
        return $this->success(
            DB::table('fixed_asset_types as t')
            ->select(
                't.id',
                't.category',
                't.name',
                't.useful_life_niif',
                't.useful_life_pcga',
                't.annual_depreciation_percentage_niif',
                't.annual_depreciation_percentage_pcga',
                't.state',
                'o.niif_name as niif_account_plan',
                'p.name as pcga_account_plan',
                'c.name as pcga_account_plan_debit_depreciation',
                'a.niif_name as niif_account_plan_debit_depreciation',
                'n.niif_name as niif_account_plan_credit_depreciation',
                'l.name as pcga_account_plan_credit_depreciation',
            )
            ->join('account_plans as a', 'a.id', '=', 't.niif_account_plan_debit_depreciation_id')
            ->join('account_plans as c', 'c.id', '=', 't.pcga_account_plan_debit_depreciation_id')
            ->join('account_plans as o', 'o.id', '=', 't.niif_account_plan_id')
            ->join('account_plans as p', 'p.id', '=', 't.pcga_account_plan_id')
            ->join('account_plans as n', 'n.id', '=', 't.niif_account_plan_credit_depreciation_id')
            ->join('account_plans as l', 'l.id', '=', 't.pcga_account_plan_credit_depreciation_id')
            ->when( request()->get('name') , function($q, $fill)
            {
                $q->where('name','like','%'.$fill.'%');
            })
            ->when( request()->get('category') , function($q, $fill)
            {
                $q->where('category','like','%'.$fill.'%');
            })
            ->when( request()->get('useful_life_niif') , function($q, $fill)
            {
                $q->where('useful_life_niif','like','%'.$fill.'%');
            })
            ->when( request()->get('depreciation') , function($q, $fill)
            {
                $q->where('annual_depreciation_percentage_niif','like','%'.$fill.'%');
            })
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
        try {
            return $this->success(
                FixedAssetType::updateOrCreate(['id' => $request->get('id')], $request->all())
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
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
