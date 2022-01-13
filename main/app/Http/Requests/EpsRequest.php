<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpsRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
            'code' => 'unique|min:2',
            'nit' => 'unique'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Debes ingresar un code '
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // if ($this->somethingElseIsInvalid()) {
            $validator->errors()->add('field', 'Something is wrong with this field!');
            // }
        });
    }
}
