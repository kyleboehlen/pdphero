<?php

namespace App\Http\Requests\Habits;

use Illuminate\Foundation\Http\FormRequest;

// Rules
use App\Rules\Habits\DaysOfWeekArray;

class StoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'days-of-week' => ['required_without:every-x-days', new DaysOfWeekArray()],
            'every-x-days' => 'required_without:days-of-week|numeric|between:1,10',
            'times-daily' => 'required|numeric|between:1,100',
            // Checkbox - show-todo
            'notes' => 'nullable',
        ];
    }
}
