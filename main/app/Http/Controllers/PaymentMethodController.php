<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Database\Query\Builder;

class PaymentMethodController extends Controller
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
                PaymentMethod::orderBy('name', 'DESC')->get(['name As text', 'id As value']));
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

    public function paginate()
    {
        try {
            return $this->success(
                PaymentMethod::orderBy('name')
                    ->when(request()->get('name'), function (Builder $q) {
                        $q->where('name', 'like', '%' . request()->get('name') . '%');
                    })
                    ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
            );
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
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
            $dep = PaymentMethod::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($dep->wasRecentlyCreated) ? $this->success('creado con exito') : $this->success('actualizado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentMethod  $PaymentMethod
     * @return \Illuminate\Http\Response
     */

     public function getPaymentMethodContractId($id){
        try{
            
            return Contract::with('payment_method')->Where('id',$id)->get();         
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
     }
    public function show($id)
    {
        try{
            $PaymentMethod = PaymentMethod::findOrFail($id);
            // return response()->json( $administrator);
            return PaymentMethod::with('contracts')->find(1);
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentMethod  $PaymentMethod
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethod $PaymentMethod)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentMethod  $PaymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

       
            $PaymentMethod = PaymentMethod::findOrFail($id);
            $PaymentMethod->name = $request->name;
            $PaymentMethod->description = $request->description;
            
            $PaymentMethod->save();
            return response()->json('Modalidad de pago actualizada correctamente');
            
         }catch(\Throwable $th){
             return response()->json([$th->getMessage(), $th->getLine()]);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentMethod  $PaymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $PaymentMethod = PaymentMethod::findOrFail($id);
            $PaymentMethod->delete();
            return response()->json('Modalida de pago eliminada correctamente');
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
