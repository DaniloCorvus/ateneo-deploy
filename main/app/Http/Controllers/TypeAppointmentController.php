<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeAppointmentResource;
use App\Models\TypeAppointment;
use Illuminate\Http\Request;

class TypeAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($typeAppointment = '')
    {
        if ($typeAppointment == 'undefined' || is_numeric($typeAppointment) || $typeAppointment == 'getall') {

            return TypeAppointmentResource::collection(TypeAppointment::all());
        }
        return TypeAppointmentResource::collection(TypeAppointment::where('name', 'like', '%' . $typeAppointment . '%')->get());
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
     * @param  \App\TypeAppointment  $typeAppointment
     * @return \Illuminate\Http\Response
     */
    public function show(TypeAppointment $typeAppointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeAppointment  $typeAppointment
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeAppointment $typeAppointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeAppointment  $typeAppointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeAppointment $typeAppointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeAppointment  $typeAppointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeAppointment $typeAppointment)
    {
        //
    }
}
