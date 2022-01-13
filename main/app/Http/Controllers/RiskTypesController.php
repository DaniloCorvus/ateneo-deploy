<?php

namespace App\Http\Controllers;

use App\Models\RiskTypes;
use App\Models\TypeRisk;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class RiskTypesController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(RiskTypes::all(['name As text', 'id As value']));
    }


    public function paginate()
    {
        return $this->success(
            RiskTypes::when( Request()->get('risk_type') , function($q, $fill)
            {
                $q->where('risk_type','like','%'.$fill.'%');
            }
        )
        ->when( Request()->get('accounting_account') , function($q, $fill)
            {
                $q->where('accounting_account','like','%'.$fill.'%');
            }
        )
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
            $riskTypes  = RiskTypes::updateOrCreate( [ 'id'=> $request->get('id') ]  , $request->all() );
            return ($riskTypes->wasRecentlyCreated) ? $this->success('Creado con exito') : $this->success('Actualizado con exito');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function show(RiskTypes $riskTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function edit(RiskTypes $riskTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RiskTypes $riskTypes)
    {
        try {
            $riskTypes = RiskTypes::find(request()->get('id'));
            $riskTypes->update(request()->all());
            return $this->success('Tipo de riesgo actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeRisk  $typeRisk
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $riskTypes = RiskTypes::findOrFail($id);
            $riskTypes->delete();
            return $this->success('Tipo de riesgo eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
