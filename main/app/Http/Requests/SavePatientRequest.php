<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePatientRequest extends FormRequest
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
            // ' Id_Llamada' => 'required',
            // 'Identificacion_Paciente' => 'required',
            // 'Identificacion_Agente' => 'required',
            // 'Tipo_Tramite' => 'required',
            // 'Tipo_Servicio' => 'required',
            // 'Ambito' => 'required'
        ];
    }
}
