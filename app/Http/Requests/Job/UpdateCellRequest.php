<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateCellRequest extends FormRequest
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
            'Name' => 'nullable',
            'CustomerID' => 'nullable|integer|min:1|exists:Customer,ID',
            'MethodID' => 'nullable|integer|min:1|exists:JMethod,ID',
            'TypeID' => 'nullable|integer|min:1|exists:JType,ID',
            'StartDate' => ['nullable', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    $date = date('Y-m-d', strtotime($value));
                    if ($date == $value) {
                        $date = Carbon::make($value);
                        $finish = Job::find($this->validationData()['ID']);
                        if ($finish->FinishDate != self::DEFAULT_DATE) {
                            $finishDate = Carbon::make($finish->FinishDate);
                            if ($date > $finishDate) {
                                return $fail('The ' . $attribute . ' must be default value or before or equal finish date.');
                            }
                        }
                    }
                }
            }],
            'Deadline' => ['nullable', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    $date = date('Y-m-d', strtotime($value));
                    if ($date == $value) {
                        $date = Carbon::make($value);
                        $start = Job::find($this->validationData()['ID']);
                        $startDate = Carbon::make($start->StartDate);
                        if ($date < $startDate) {
                            return $fail('The ' . $attribute . ' must be default value or after or equal start date.');
                        }
                    }
                }
            }],
            'FinishDate' => ['nullable', function ($attribute, $value, $fail) {
                if ($value != self::DEFAULT_DATE) {
                    $date = date('Y-m-d', strtotime($value));
                    if ($date == $value) {
                        $date = Carbon::make($value);
                        $start = Job::find($this->validationData()['ID']);
                        $startDate = Carbon::make($start->StartDate);
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
            'PriceYen' => 'nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'CustomerID.exists' => 'Customer not found.',
            'TypeID.exists' => 'Type not found.',
            'MethodID.exists' => 'Method not found.',
            'FinishDate.after' => 'Finish Date must greater than Start Date.',
            'Deadline.after' => 'Deadline must greater than Start Date.',
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
        $err = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            response()->json(
                [
                    'err' => $err
                ],
                422
            )
        );
    }
}
