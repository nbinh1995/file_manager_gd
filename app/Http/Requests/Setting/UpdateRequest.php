<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'unpaid-amount' => 'nullable|integer|min:1',
            'keep-days' => 'nullable|integer|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'unpaid-amount' => 'unpaid amount',
            'keep-days' => 'keep day(s)',
        ];
    }
}
