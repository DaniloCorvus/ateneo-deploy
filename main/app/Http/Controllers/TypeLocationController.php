<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Models\TypeLocation;
use Illuminate\Http\Request;

class TypeLocationController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(TypeLocation::orderBy('name', 'DESC')->get(['name As text', 'id As value']));

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
     * @param  \App\TypeLocation  $typeLocation
     * @return \Illuminate\Http\Response
     */
    public function show(TypeLocation $typeLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeLocation  $typeLocation
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeLocation $typeLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeLocation  $typeLocation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeLocation $typeLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeLocation  $typeLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeLocation $typeLocation)
    {
        //
    }
}
