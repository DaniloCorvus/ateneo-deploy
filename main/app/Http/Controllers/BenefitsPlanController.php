<?php

namespace App\Http\Controllers;

use App\Models\BenefitsPlan;
use App\Models\Contract;
use Illuminate\Database\Query\Builder;
use App\Traits\ApiResponser;

use Illuminate\Http\Request;

class BenefitsPlanController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->success(
                BenefitsPlan::orderBy('name', 'DESC')->get(['name As text', 'id As value']));
            }catch (\Throwable $th) {
                return $this->error($th->getMessage(), 400);
            }
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

    public function paginate()
    {
        try {
            return $this->success(
                BenefitsPlan::orderBy('name')
                    ->when(request()->get('name'), function (Builder $q) {
                        $q->where('name', 'like', '%' . request()->get('name') . '%');
                    })
                    ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
    public function store(Request $request)
    {
        try {
            $dep = BenefitsPlan::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($dep->wasRecentlyCreated) ? $this->success('creado con exito') : $this->success('actualizado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BenefitsPlan  $BenefitsPlan
     * @return \Illuminate\Http\Response
     */

     public function getBenefitPlanContractId($id){
        try{
            
            return Contract::with('benefitsPlan')->Where('id',$id)->get();         
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
     }

     
    public function show($id)
    {
        try{
            $BenefitsPlan = BenefitsPlan::findOrFail($id);
            // return response()->json( $administrator);
            return BenefitsPlan::with('contracts')->find(2);
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BenefitsPlan  $BenefitsPlan
     * @return \Illuminate\Http\Response
     */
    public function edit(BenefitsPlan $BenefitsPlan)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BenefitsPlan  $BenefitsPlan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id )
    {
        try{
           
          $BenefitsPlan = BenefitsPlan::findOrFail($id);
          $BenefitsPlan->name = $request->name;
          $BenefitsPlan->description = $request->description;
          $BenefitsPlan->save();
          return response()->json('Plan de beneficio actualizado correctamente');   
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BenefitsPlan  $BenefitsPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $BenefitsPlan = BenefitsPlan::findOrFail($id);
            $BenefitsPlan->delete();
            return response()->json('plan de beneficios eliminado correctamente');
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
    
}
