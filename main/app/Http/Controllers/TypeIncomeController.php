<?php

namespace App\Http\Controllers;

use App\Models\TypeIncome;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeIncomeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeIncome::get(['name As text', 'id As value']));
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
            $typeIncome  = TypeIncome::create($request->all());
            return $this->success(['message' => 'Tipo de ingreso creado correctamente', 'model' => $typeIncome]);
            // return response()->json('Sede creada correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TypeIncome  $typeIncome
     * @return \Illuminate\Http\Response
     */
    public function show(TypeIncome $typeIncome)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TypeIncome  $typeIncome
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeIncome $typeIncome)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TypeIncome  $typeIncome
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeIncome $typeIncome)
    {
        try {
            $typeIncome = TypeIncome::find(request()->get('id'));
            $typeIncome->update(request()->all());
            return $this->success('Tipo de ingreso actualizado correctamente');
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TypeIncome  $typeIncome
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $typeIncome = TypeIncome::findOrFail($id);
            $typeIncome->delete();
            return $this->success('Tipo de ingreso eliminado correctamente', 204);
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage(), $th->getLine()]);
        }
    }
}
