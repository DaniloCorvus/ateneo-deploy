<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfessionalRequest extends FormRequest
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
        $rules = [
            // 'identifier' => 'required|numeric|unique:people',
            // 'external_id' => 'required|unique:people',
            // 'first_name' => 'required|string',
            // 'second_name' => 'string',
            // 'first_surname' => 'required|string',
            // 'second_surname' => 'string',
            // 'cellphone' => 'required|string',
            // 'email' => 'required|email',
            // 'address' => 'required|string',
            // 'people_type_id' => 'required|numeric',
            // 'status' => 'required|string',
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {

            $rules = [
                'identifier' => 'required|unique:people,' . $this->id,
                'external_id' => 'required|unique:people,' . $this->external_id,
            ];
        }

        return $rules;
    }
}
