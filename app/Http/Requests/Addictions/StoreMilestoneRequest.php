<?php

namespace App\Http\Requests\Addictions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilestoneRequest extends FormRequest
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
        $date_format_values = array_keys(config('addictions.date_formats'));

        return [
            'name' => 'required|string|max:255',
            'milestone-amount' => 'required|numeric|min:1',
            'milestone-date-format' => ['required', Rule::in($date_format_values)],
            'reward' => 'nullable',
        ];
    }
}
