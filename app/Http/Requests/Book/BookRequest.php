<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->is_admin ? true :false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filename' => ['required','string','regex:/^[^\d][^~`\'\",.-]+/','max:255','unique:books,filename',function ($attribute, $value, $fail) {
                $path = config('lfm.public_dir'). convert_name($value);
                if(File::exists(config('filesystems.disks.private.root').'/'.$path)){
                    return $fail('The  path filename  was exists!');
                }
            }],
        ];
    }
}
