<?php

namespace App\Http\Controllers;

use App\Models\RegimenType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class RegimenTypeController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(RegimenType::get(['name As text', 'id As value']));
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
     * @param  \App\Models\RegimenType  $regimenType
     * @return \Illuminate\Http\Response
     */
    public function show(RegimenType $regimenType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RegimenType  $regimenType
     * @return \Illuminate\Http\Response
     */
    public function edit(RegimenType $regimenType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RegimenType  $regimenType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegimenType $regimenType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RegimenType  $regimenType
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegimenType $regimenType)
    {
        //
    }
}
