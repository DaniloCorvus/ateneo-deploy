<?php

namespace App\Http\Controllers;

use App\Models\TypeReturn;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeReturnController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeReturn::get(['name As text', 'id As value']));
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
            $typeReturn  = TypeReturn::create($request->all());
            return $this->success(['message' => 'Tipo de devolucion creado correctamente', 'model' => $typeReturn]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeReturn  $typeReturn
     * @return \Illuminate\Http\Response
     */
    public function show(TypeReturn $typeReturn)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeReturn  $typeReturn
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeReturn $typeReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeReturn  $typeReturn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeReturn $typeReturn)
    {
        try {
            $typeReturn = TypeReturn::find(request()->get('id'));
            $typeReturn->update(request()->all());
            return $this->success('Tipo de devolucion actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeReturn  $typeReturn
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeReturn = TypeReturn::findOrFail($id);
            $typeReturn->delete();
            return $this->success('Tipo de devolucion eliminado correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
