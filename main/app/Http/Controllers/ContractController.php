<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $contract = Contract::query();

        $contract->when(request()->get('department_id'), function (Builder $q) {
            $q->where('department_id', request()->get('department_id'));
        });

        $contract->when(request()->get('eps_id'), function (Builder $q) {
            $q->where(function (Builder $q) {
                $q->where('administrator_id', request()->get('eps_id'))
                    ->orWhere('regimen_id', request()->get('regimen_id'));
            });
        });

        $contract->when(request()->get('company_id'), function (Builder $q) {
            $q->where('company_id', request()->get('company_id'));
        });


        $result = $contract->get(['name As text', 'id As value']);

        return $this->success($result);
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
        //
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
