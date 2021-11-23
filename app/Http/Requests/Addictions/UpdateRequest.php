<?php

namespace App\Http\Requests\Addictions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Constants
use App\Helpers\Constants\Addiction\Method;

class UpdateRequest extends FormRequest
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
        $moderation = Method::MODERATION;
        $method_ids = array_keys(config('addictions.methods'));
        $date_format_values = array_keys(config('addictions.date_formats'));

        return [
            'name' => 'required|string|max:255',
            'start-date' => 'required|before:tomorrow',
            'method' => ['required', Rule::in($method_ids)],
            'moderation-amount' => "required_if:method,$moderation|numeric|min:1|max:10",
            'moderation-period' => "required_if:method,$moderation|numeric|min:1|max:10",
            'moderation-date-format' => ["required_if:method,$moderation", Rule::in($date_format_values)],
            'details' => 'required',
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
            'start-date.before' => 'If you\'re going to quit, quit today.',
        ];
    }
}
