<?php

namespace App\Http\Controllers;

use App\Models\TypeFixedAsset;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeFixedAssetController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeFixedAsset::get(['name As text', 'id As value']));
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
            $typeFixedAsset  = TypeFixedAsset::create($request->all());
            return $this->success(['message' => 'Tipo de activo fijo creado correctamente', 'model' => $typeFixedAsset]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeFixedAsset  $typeFixedAsset
     * @return \Illuminate\Http\Response
     */
    public function show(TypeFixedAsset $typeFixedAsset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeFixedAsset  $typeFixedAsset
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeFixedAsset $typeFixedAsset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeFixedAsset  $typeFixedAsset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeFixedAsset $typeFixedAsset)
    {
        try {
            $typeFixedAsset = TypeFixedAsset::find(request()->get('id'));
            $typeFixedAsset->update(request()->all());
            return $this->success('Tipo de activo fijo actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeFixedAsset  $typeFixedAsset
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeFixedAsset = TypeFixedAsset::findOrFail($id);
            $typeFixedAsset->delete();
            return $this->success('Tipo de activo fijo eliminado correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
