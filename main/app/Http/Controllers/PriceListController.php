<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\Contract;
use App\Models\Cup;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Database\Query\Builder;
class PriceListController extends Controller
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
                PriceList::orderBy('cum', 'DESC')->get(['cum As text', 'id As value']));
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
                PriceList::orderBy('cum')
                    ->when(request()->get('cum'), function (Builder $q) {
                        $q->where('cum', 'like', '%' . request()->get('cum') . '%');
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
            $dep = PriceList::updateOrCreate(['id' => request()->get('id')], request()->all());
            return ($dep->wasRecentlyCreated) ? $this->success('creado con exito') : $this->success('actualizado con exito');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PriceList  $PriceList
     * @return \Illuminate\Http\Response
     */

     public function getPriceListContractsId($id){
        try{
            
            return Contract::with('priceList')->Where('id',$id)->get();         
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
     }

     public function getCupId($id){
        try{
            
            return Cup::with('priceList')->Where('id',$id)->get();         
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }

     }
    public function show($id)
    {
        

        try{
            $PriceList = PriceList::findOrFail($id);
            // return response()->json( $administrator);
            return PriceList::with('contracts','cup')->find(1);
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PriceList  $PriceList
     * @return \Illuminate\Http\Response
     */
    public function edit(PriceList $PriceList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PriceList  $PriceList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{

       
            $PriceList = PriceList::findOrFail($id);
            $PriceList->cup_id = $request->cup_id;
            $PriceList->cum = $request->cum;
            $PriceList->price = $request->price;
            $PriceList->save();
            return response()->json('Lista de precios actualizada correctamente');
            
         }catch(\Throwable $th){
             return response()->json([$th->getMessage(), $th->getLine()]);
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PriceList  $PriceList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $PriceList = PriceList::findOrFail($id);
            $PriceList->delete();
            return response()->json('Lista de precios eliminada correctamente');
        }catch(\Throwable $th){
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
