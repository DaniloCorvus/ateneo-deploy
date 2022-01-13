<?php

namespace App\Http\Controllers;

use App\Models\TypePqrs;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypePqrsController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypePqrs::get(['name As text', 'id As value']));
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
            $typePqrs  = TypePqrs::create($request->all());
            return $this->success(['message' => 'Tipo de PQRS creado correctamente', 'model' => $typePqrs]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypePqrs  $typePqrs
     * @return \Illuminate\Http\Response
     */
    public function show(TypePqrs $typePqrs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypePqrs  $typePqrs
     * @return \Illuminate\Http\Response
     */
    public function edit(TypePqrs $typePqrs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypePqrs  $typePqrs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypePqrs $typePqrs)
    {
        try {
            $typePqrs = TypePqrs::find(request()->get('id'));
            $typePqrs->update(request()->all());
            return $this->success('Tipo de PQRS actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypePqrs  $typePqrs
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typePqrs = TypePqrs::findOrFail($id);
            $typePqrs->delete();
            return $this->success('Tipo de PQRS eliminado correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
