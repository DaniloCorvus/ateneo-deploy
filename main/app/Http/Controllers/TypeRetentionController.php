<?php

namespace App\Http\Controllers;

use App\Models\TypeRetention;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeRetentionController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeRetention::get(['name As text', 'id As value']));
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
            $typeRetention  = TypeRetention::create($request->all());
            return $this->success(['message' => 'Tipo de retencion creado correctamente', 'model' => $typeRetention]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeRetention  $typeRetention
     * @return \Illuminate\Http\Response
     */
    public function show(TypeRetention $typeRetention)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeRetention  $typeRetention
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeRetention $typeRetention)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeRetention  $typeRetention
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeRetention $typeRetention)
    {
        try {
            $typeRetention = TypeRetention::find(request()->get('id'));
            $typeRetention->update(request()->all());
            return $this->success('Tipo de retencion actualizada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeRetention  $typeRetention
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeRetention = TypeRetention::findOrFail($id);
            $typeRetention->delete();
            return $this->success('Tipo de retencion eliminada correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
