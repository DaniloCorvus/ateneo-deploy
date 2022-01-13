<?php

namespace App\Http\Controllers;

// use App\CallIn;

// use App\CallIn;
use App\Http\Requests\PatientSaveRequest;
use App\Models\CallIn;
use App\Models\Patient;
use App\Traits\ApiResponser;
use App\Traits\manipulateDataFromExternalService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;


class PatientController extends Controller
{

    use ApiResponser, manipulateDataFromExternalService;
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
    public function store(PatientSaveRequest $request)
    {

        try {
            $patient =   Patient::firstWhere('identifier', request('identifier'));
            request()->merge(['regional_id' => $this->appendRegional(request()->get('department_id'))]);

            if ($patient) {
                $patient->update($request->all());
            } else {
                $patient  = Patient::create(request()->all());
            }

            return $this->success(['message' => 'Actualizacion existosa', 'patient' => $patient]);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        try {
            $patient->update(request()->all());
            return $this->success('Registro existoso');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        //
    }

    public function getPatientInCall()
    {
        try {

            $patient = null;

            $call = CallIn::Where('status', 'Pendiente')
                ->where('type', 'CallCenter')
                ->where('Identificacion_Agente', auth()->user()->usuario)
                ->first();

            if ($call) {

                $patient = Patient::with(
                    'eps',
                    'company',
                    'municipality',
                    'department',
                    'regional',
                    'level',
                    'regimentype',
                    'typedocument',
                    'contract',
                    'location'
                )->firstWhere('identifier', $call->Identificacion_Paciente);
            }

            return $this->success(['paciente' => $patient, 'llamada' => $call]);
        } catch (\Throwable $th) {
            return $this->success($th->getMessage(), 201);
        }
    }

    public function getPatientResend($id)
    {
        try {
            $patient = Patient::with(
                'eps',
                'company',
                'municipality',
                'department',
                'regional',
                'level',
                'regimentype',
                'typedocument',
                'contract',
                'location'
            )->firstWhere('identifier', $id);
            return $this->success($patient);
        } catch (\Throwable $th) {
            return $this->success($th->getMessage(), 201);
        }
    }
}
