<?php

namespace App\Http\Controllers;

use App\Fee;
use App\Models\Appointment;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    use ApiResponser;
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

            switch (request()->get('cuota')) {
                case 0:
                    $fee = Fee::create([
                        "appointment_id" => request()->get('appointment_id'),
                        "price"  => request()->get('cuota'),
                        "reason"  => request()->get('causal'),
                        "observation"  => request()->get('observaciones'),
                    ]);
                    break;

                default:
                    $fee = Fee::create([
                        "appointment_id" => request()->get('appointment_id'),
                        "payment_method_id"  => request()->get('method_pay'),
                        "bank_id" => request()->get('bank', 0),
                        "price"  => request()->get('cuota'),
                        "reason"  => request()->get('causal'),
                        "observation"  => request()->get('observaciones'),
                    ]);
                    break;
            }

            if ($fee) {
                $ap = Appointment::findOrfail(request()->get('appointment_id'));
                $ap->state = 'SalaEspera';
                $ap->payed = 0;
                $ap->save();
            }

            return response()->success('recurso creado correctamente', 201);
        } catch (\Throwable $th) {
            return response()->error([$th->getMessage(), $th->getLine(), $th->getFile()], 400);
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
