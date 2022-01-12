<?php

namespace App\Http\Controllers;

// use App\CallIn;

use App\Models\CallIn;
use App\Models\Patient;
use App\Models\WaitingList;
use App\Traits\ApiResponser;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;

class CallInController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CallIn  $callIn
     * @return \Illuminate\Http\Response
     */
    public function show(CallIn $callIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CallIn  $callIn
     * @return \Illuminate\Http\Response
     */
    public function edit(CallIn $callIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CallIn  $callIn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CallIn $callIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CallIn  $callIn
     * @return \Illuminate\Http\Response
     */
    public function destroy(CallIn $callIn)
    {
        //
    }

    public  function presentialCall(Request $request)
    {
        try {
            $data = $request->all();
            $data['Identificacion_Agente'] =  auth()->user()->usuario;
            $call = CallIn::create($data);

            $patient = Patient::with('eps', 'company', 'municipality', 'department', 'regional', 'level', 'regimentype', 'typedocument', 'contract')->firstWhere('identifier', $call->Identificacion_Paciente);
            $isNew = false;
            if (!$patient) {
                $isNew = true;
            }
            return $this->success(['paciente' => $patient, 'llamada' => $call, 'isNew' => $isNew]);
        } catch (\Throwable $th) {
            return $this->success($th->getMessage(), 201);
        }
        return $this->success($call);
    }

    public  function patientforwaitinglist(Request $request)
    {
        try {
            $data =  WaitingList::with('appointment', 'appointment.callin', 'appointment.cup:description As text,id,id As value', 'appointment.cie10:description As text,id,id As value')->find(request()->get('0'));
            $patient = Patient::with('eps', 'company', 'municipality', 'department', 'regional', 'level', 'regimentype', 'typedocument', 'contract')->firstWhere('identifier', $data->appointment->callin->Identificacion_Paciente);
            $isNew = false;
            return $this->success(['paciente' => $patient, 'llamada' => $data->appointment->callin, 'isNew' => $isNew, 'anotherData' =>  $data]);
        } catch (\Throwable $th) {
            return $this->success($th->getMessage(), 400);
        }
    }
}
