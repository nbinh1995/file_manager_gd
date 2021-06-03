<?php

namespace App\Http\Requests\Customer;

use App\Models\Customer;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class CustomerUpdateRequest extends FormRequest
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
            'Name' => 'nullable|string|max:50',
            'Note' => 'nullable'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $err = (new ValidationException($validator))->errors();
        $id = $this->validationData()['id'];
        $customer = Customer::find($id);
        $oldName = $this->validationData()['Name'] ? $this->validationData()['Name'] : '';

        $html = view('partials.form.form-edit_customer', ['err' => $err, 'customer' => $customer, 'oldName' => $oldName])->render();

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
