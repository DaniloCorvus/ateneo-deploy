<?php

namespace App\Http\Controllers;

use App\Models\DrivingLicense;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class DrivingLicenseController extends Controller
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
            DrivingLicense::where('state', '=', 'activo')
            ->get(['id as value', 'type as text'])
        );
    }

    public function paginate()
    {
        return $this->success(
            DrivingLicense::orderBy('type')
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
            $license = DrivingLicense::updateOrCreate(['id' => $request->get('id')], $request->all());
            return ($license->wasRecentlyCreated) 
                ? 
                $this->success([
                'title' => '¡Creado con éxito!',
                'text' => 'La Licencia de Conducción ha sido creada satisfactoriamente'
                ]) 
                : 
                $this->success([
                'title' => '¡Actualizado con éxito!',
                'text' => 'La Licencia de Conducción ha sido Actualizada satisfactoriamente'
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
