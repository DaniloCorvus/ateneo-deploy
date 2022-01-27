<?php

namespace App\Http\Controllers;

use App\MethodPay;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Mockery\Generator\Method;

class MethodPayController extends Controller
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
            MethodPay::orderBy('name', 'DESC')
                ->get(['name As name', 'id As value'])
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MethodPay  $methodPay
     * @return \Illuminate\Http\Response
     */
    public function show(MethodPay $methodPay)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MethodPay  $methodPay
     * @return \Illuminate\Http\Response
     */
    public function edit(MethodPay $methodPay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MethodPay  $methodPay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MethodPay $methodPay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MethodPay  $methodPay
     * @return \Illuminate\Http\Response
     */
    public function destroy(MethodPay $methodPay)
    {
        //
    }
}
