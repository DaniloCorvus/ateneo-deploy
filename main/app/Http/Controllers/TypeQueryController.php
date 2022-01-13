<?php

namespace App\Http\Controllers;

use App\Models\TypeQuery;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeQueryController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeQuery::get(['name As text', 'id As value']));
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
            $typeQuery  = TypeQuery::create($request->all());
            return $this->success(['message' => 'Tipo de Consulta creada correctamente', 'model' => $typeQuery]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeQuery  $typeQuery
     * @return \Illuminate\Http\Response
     */
    public function show(TypeQuery $typeQuery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeQuery  $typeQuery
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeQuery $typeQuery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeQuery  $typeQuery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeQuery $typeQuery)
    {
        try {
            $typeQuery = TypeQuery::find(request()->get('id'));
            $typeQuery->update(request()->all());
            return $this->success('Tipo de consulta actualizada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeQuery  $typeQuery
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeQuery = TypeQuery::findOrFail($id);
            $typeQuery->delete();
            return $this->success('Tipo de consulta eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
