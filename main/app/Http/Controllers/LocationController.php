<?php

namespace App\Http\Controllers;

use App\Http\Resources\SedeResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // ${this.company}/${this.subappointment.procedure

    public function index($company)
    {
        return SedeResource::collection(Location::where('company_id', $company)->get(['id', 'name']));
        // return SedeResource::collection(Sede::where('company_id', $company)->where('allow_procedure', $allowprocedure)->get(['id', 'name']));
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
     * @param  App\Models\Location  $sede
     * @return \Illuminate\Http\Response
     */
    public function show(Location $sede)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  App\Models\Location  $sede
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $sede)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\Location  $sede
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $sede)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Location  $sede
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $sede)
    {
        //
    }
}
