<?php

namespace App\Http\Requests\Journal;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'keywords' => 'required|string|max:255',
            'start-date' => 'required|date_format:Y-m-d|before_or_equal:end-date',
            'end-date' => 'required|date_format:Y-m-d|after_or_equal:start-date',
        ];
    }
}
