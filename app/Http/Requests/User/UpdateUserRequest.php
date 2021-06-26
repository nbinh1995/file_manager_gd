<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username' => 'required|string|min:1',
            'password' => 'nullable|string|min:1|confirmed',
            'role' => 'required|in:'.implode(',',config('lfm.volume')),
            'role_multi.*' => 'required|in:'.implode(',',config('lfm.volume')),
        ];
    }
}
