<?php

namespace App\Http\Requests\JType;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class JTypeRequest extends FormRequest
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
            'Name' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $err = (new ValidationException($validator))->errors();
        $html = view('partials.form.form-create_jtype', ['err' => $err])->render();
        throw new HttpResponseException(
            response()->json(
                [
                    'html' => $html
                ],
                400
            )
        );
    }
}
