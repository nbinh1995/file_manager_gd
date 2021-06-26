<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserRequest extends FormRequest
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
    public function rules(Request $request)
    {
        // dd($request->all());
        return [
            'username' => 'required|string|min:1',
            'password' => 'required|string|min:1|confirmed',
            'email' => 'required|string|unique:users,email',
            'role' => 'required|in:'.implode(',',config('lfm.volume')),
            'role_multi.*' => 'required|in:'.implode(',',config('lfm.volume')),
        ];
    }
}
