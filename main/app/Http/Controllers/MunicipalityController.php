<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Municipality::orderBy('name', 'DESC')
        ->when(Request()->get('department_id'),function($q,$param){
        
            $q->where('department_id',$param);
        })
        ->get(['name As text', 'id As value']);
        return $this->success($data);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Municipality  $city
     * @return \Illuminate\Http\Response
     */
    public function show(Municipality $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Municipality  $city

     * @return \Illuminate\Http\Response
     */
    public function edit(Municipality $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Municipality  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Municipality $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Municipality  $city

     * @return \Illuminate\Http\Response
     */
    public function destroy(Municipality $city)
    {
        //
    }
}
