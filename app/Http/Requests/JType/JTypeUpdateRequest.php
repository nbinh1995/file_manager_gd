<?php

namespace App\Http\Requests\JType;

use App\Models\JType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class JTypeUpdateRequest extends FormRequest
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
        $id = $this->validationData()['id'];
        $jtype = JType::find($id);
        $oldName = $this->validationData()['Name'] ? $this->validationData()['Name'] : '';

        $html = view('partials.form.form-edit_jtype', ['err' => $err, 'jtype' => $jtype, 'oldName' => $oldName])->render();

        throw new HttpResponseException(
            response()->json(
                [
                    'html' => $html
                ],
                422
            )
        );
    }
}
