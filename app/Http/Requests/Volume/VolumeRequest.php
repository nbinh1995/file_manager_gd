<?php

namespace App\Http\Requests\Volume;

use App\Models\Book;
use App\Models\Volume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;

class VolumeRequest extends FormRequest
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
            'filename' => ['required','string','regex:/^[^\d][^~`\'\",.-]+/','max:255',function ($attribute, $value, $fail) {
                if(Volume::where('book_id',$this->validationData()['book_id'])->where('filename',$value)->exists()){
                    return $fail('The  filename  was exists!');
                }
            }],function ($attribute, $value, $fail) {
                $path = Book::find($this->validationData()['book_id'])->path.'/'.convert_name($value);
                if(File::exists(config('filesystems.disks.private.root').'/'.$path)){
                    return $fail('The  path filename  was exists!');
                }
            },
            'book_id' => 'required|exists:books,id'
        ];
    }
}
