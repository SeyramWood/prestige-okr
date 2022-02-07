<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObjectiveRequest extends FormRequest
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
            'title' => 'required|string',
            'tag' => 'nullable|string',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'team' => 'exclude_unless:type,team|required',
            'period.when' => 'required|string',
            'period.startDate' => 'required|string',
            'period.endDate' => 'required|string',
            'progress.type' => 'required|string',
            'progress.option' => 'required|string',
            'unit' => 'required|string',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'period.when' => 'when',
            'period.startDate' => 'start date',
            'period.endDate' => 'end date',
            'progress.type' => 'type',
            'progress.option' => 'option',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
