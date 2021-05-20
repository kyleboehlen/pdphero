<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionItemRequest extends FormRequest
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
            'name' => 'string|max:255',
            'notes' => 'nullable',
            'deadline' => 'date_format:Y-m-d',
            'override-show-todo' => 'required_with:show-todo',
            'show-todo' => 'required_with:show-todo-days-before',
            'show-todo-days-before' => 'required_with:show-todo|numeric|min:0|max:60',
        ];
    }
}
