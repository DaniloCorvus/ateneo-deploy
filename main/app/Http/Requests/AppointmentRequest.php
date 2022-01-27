<?php

namespace App\Http\Requests;

use App\Rules\emailCustom;
use App\Rules\PatientExist;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'call' => 'required',
            'diagnosticoId.value' => 'required|exists:cie10s,id',
            'person_remisor' => 'required',
            'ips_remisor' =>  'required',
            'especiality' =>  'required',
            'date_remisor' =>  'required',
            'procedureId.value' =>  'required|exists:cups,id',
            'observation' => 'required',
            'patient.email' => ['required', 'email', new emailCustom],
            'patient.identifier' => 'required|exists:patients,identifier',
            'patient.contract_id' => 'required',
            'patient.address' => 'required',
            'patient.gener' => 'required',
            'patient.location_id' => 'required',
            'patient.date_of_birth' => 'required',
            'patient.department_id' => 'required',
            'patient.eps_id' => 'required',
            'patient.level_id' => 'required',
            'patient.municipality_id' => 'required',
            'patient.regimen_id' => 'required',
            'patient.type_document_id' => 'required',
            'patient.phone' => 'required',
            'patient.identifier' => [new PatientExist]

        ];
    }

    public function messages()
    {
        return [
            'procedureId.value.required' => 'Debe elegir un procedimiento valido',
            'diagnosticoId.value.required' => 'Debe elegir un diagnostico valido',
            'patient.email.email' => 'Debe elegir un email valido',
            'diagnosticoId.required'  => 'Debe elegir un diagnostico valido',
            'patient.identifier.exists' => 'Debes guardar los datos del paciente antes de generar la cita',
            'patient.identifier.required' => 'Debes guardar los datos del paciente antes de generar la cita',
            'patient.contract_id.required' => 'Debes elegir un contrato',
            'patient.address.required' => 'Debes elegir una dirección',
            'patient.gener.required' => 'Debes elegir un género',
            'patient.date_of_birth.required' => 'Debes elegir una fecha de cumpleaños',
            'patient.department_id.required' => 'Debes elegir un departamento',
            'patient.eps_id.required' => 'Debes elegir una administradora',
            'patient.level_id.required' => 'Debes elegir un nivel',
            'patient.municipality_id.required' => 'Debes elegir un municipio',
            'patient.regimen_id.required' => 'Debes elegir un regimen',
            'patient.type_document_id.required' => 'Debes elegir un tipo de documento',
            'patient.phone.required' => 'Debes elegir un telefono'
        ];
    }
}
