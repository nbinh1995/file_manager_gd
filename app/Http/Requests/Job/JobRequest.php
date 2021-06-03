<?php

namespace App\Http\Requests\Job;

use App\Models\Customer;
use App\Models\JMethod;
use App\Models\JType;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class JobRequest extends FormRequest
{

    const DEFAULT_DATE = '1111-11-11';

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
            'Name' => 'required',
            'CustomerID' => 'required|integer|exists:Customer,ID',
            'MethodID' => 'required|integer|exists:JMethod,ID',
            'TypeID' => 'required|integer|exists:JType,ID',
            'StartDate' => ['required', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    if (!date('Y-m-d', strtotime($value))) {
                        return $fail('The ' . $attribute . ' not a valid date.');
                    }
                }
            }],
            'Deadline' => ['nullable', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    $date = date('Y-m-d', strtotime($value));
                    if ($date == $value) {
                        $startDate = Carbon::make($this->validationData()['StartDate']);
                        $date = Carbon::make($date);
                        if ($date < $startDate) {
                            return $fail('The ' . $attribute . ' must be default value or after or equal start date.');
                        }
                    }
                }
            }],
            'Paydate' => ['nullable', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    if (!date('Y-m-d', strtotime($value))) {
                        return $fail('The ' . $attribute . ' not a valid date.');
                    }
                }
            }],
            'FinishDate' => ['required', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    $date = date('Y-m-d', strtotime($value));
                    if ($date == $value) {
                        $startDate = Carbon::make($this->validationData()['StartDate']);
                        $date = Carbon::make($date);
                        if ($date < $startDate) {
                            return $fail('The ' . $attribute . ' must be default value or after or equal start date.');
                        }
                    }
                }
            }],
            'PriceYen' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            '*.exists' => ':attribute not found.',
            '.after' => ':attribute must after Start Date.',
            '*.integer' => 'The value is invalid.',
        ];
    }

    public function attributes()
    {
        return [
            'CustomerID' => 'Customer',
            'TypeID' => 'Type',
            'MethodID' => 'Method',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $old = $this->validationData();
        if (!isset($errors['CustomerID'])) {
            $old['CustomerName'] = Customer::where('ID', $old['CustomerID'])->select('Name')->first()->Name;
        }
        if (!isset($errors['MethodID'])) {
            $old['MethodName'] = JMethod::where('ID', $old['MethodID'])->select('Name')->first()->Name;
        }
        if (!isset($errors['TypeID'])) {
            $old['TypeName'] = JType::where('ID', $old['TypeID'])->select('Name')->first()->Name;
        }

        throw new HttpResponseException(
            redirect()->back()->withErrors($errors)
                ->with('old', $old)
        );
    }
}
