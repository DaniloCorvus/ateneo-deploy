<?php

namespace App\Http\Controllers;

use App\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $fee = Fee::create(request()->all());
            // $fee = Fee::create([
            //     "appointment_id" => request()->get('appointment_id'),
            //     "payment_method_id"  => request()->get('appointment_id'),
            //     "bank_id" => request()->get('appointment_id'),
            //     "agenda" => request()->get('appointment_id'),
            //     "contract_id"  => request()->get('appointment_id'),
            //     "price"  => request()->get('appointment_id'),
            //     "reason"  => request()->get('appointment_id'),
            //     "observation"  => request()->get('appointment_id'),
            //     "quantity" => request()->get('appointment_id')
            // ]);
            return response()->success(['message' => 'recurso creado correctamente', 'body' => $fee], 201);
        } catch (\Throwable $th) {
            return response()->success([$th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function show(Fee $fee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function edit(Fee $fee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fee $fee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Fee  $fee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fee $fee)
    {
        //
    }
}
