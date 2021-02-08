<?php

namespace App\Http\Requests\Habits;

use Illuminate\Foundation\Http\FormRequest;

class HistoryRequest extends FormRequest
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
            'day' => 'required|date_format:Y-m-d|before_or_equal:today', // To-Do: make this required to be equal to or less than today
            'status-completed' => 'required_without_all:status-skipped,status-missed',
            'status-skipped' => 'required_without_all:status-completed,status-missed',
            'status-missed' => 'required_without_all:status-skipped,status-completed',
            'notes' => 'required_with:status-skipped|nullable',
            'times' => 'required_with:status-completed|numeric',
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
            'status-completed.required_without_all' => 'You must select a status',
            'status-skipped.required_without_all' => 'You must select a status',
            'status-missed.required_without_all' => 'You must select a status',
        ];
    }
}
