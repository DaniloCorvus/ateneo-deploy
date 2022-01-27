<?php

namespace App\Http\Controllers;

use App\Models\VisaType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class VisaTypeController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(
            VisaType::where('state', '=', 'Activo')
            ->get(['id as value', 'name as text'])
        );
    }

    public function paginate()
    {
        return $this->success(
            VisaType::orderBy('name')
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
            $visa = VisaType::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($visa->wasRecentlyCreated) 
            ? 
            $this->success([
            'title' => '¡Creado con éxito!',
            'text' => 'El Tipo de Visa ha sido creado satisfactoriamente'
            ]) 
            : 
            $this->success([
            'title' => '¡Actualizado con éxito!',
            'text' => 'El Tipo de Visa ha sido Actualizado satisfactoriamente'
            ]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
