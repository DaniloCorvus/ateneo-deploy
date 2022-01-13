<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavePatientRequest;
use App\Models\CallIn;
use App\Models\Paciente;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Validator;


class PacienteController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
    }

    public function DetallePaciente()
    {
        
        $paciente = Paciente::where('Identificacion', request()->get('Identificacion'))->first();

        if (!$paciente) {
            return $this->success(['EnBase' => 'No', 'paciente' => 'PACIENTE NO REGISTRADO EN BASE DE DATOS']);
        }
        return $this->success(['EnBase' => 'Si', 'paciente' => $paciente]);
    }

    public function DetallePacienteOld($id)
    {
        $paciente = Paciente::where('Identificacion', $id)->first();

        if (!$paciente) {
            return $this->success(['EnBase' => 'No', 'paciente' => 'PACIENTE NO REGISTRADO EN BASE DE DATOS']);
        }
        return $this->success(['EnBase' => 'Si', 'paciente' => $paciente]);
    }

    public function store(SavePatientRequest $savePatientRequest)
    {

        $validator = $this->validationCallIn($savePatientRequest->all());

        if ($validator === true) {
            CallIn::create($savePatientRequest->all());
            return $this->success('Registro existoso');
        };

        return $this->error($validator, 400);
    }

    public function ListaPacientes()
    {
        $pacientes = Paciente::paginate(100);
        return response(['pacientes' => $pacientes], 200);
    }


    public function validationCallIn($data)
    {
        $validator = Validator::make(
            $data,
            [
                'Id_Llamada' => 'required',
                'Identificacion_Paciente' => 'required',
                'Identificacion_Agente' => 'required'
            ],
            [
                'Id_Llamada.required' => 'No existe llamada ',
                'Identificacion_Paciente.required' => 'No existe Paciente ',
                'Identificacion_Agente.required' => 'No existe Agente ',
            ]

        );

        if ($validator->fails()) {
            $messages = $validator->errors();
            return $messages->first();
        }

        return true;
    }
}
