<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;

// Rules
use App\Rules\Goal\Category;

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
        // Get user
        $user = \Auth::user();

        // Get habit uuids for user
        $habits_uuids = $user->habits->pluck('uuid')->toArray();

        return [
            'name' => 'required_without:habit|string|max:255',
            'habit' => ['required_without:name', Rule::in($habits_uuids), ],
            'category' => ['required', new Category(), ],
            'custom-times' => 'required_with:time-period|numeric|min:1|max:100',
            'time-period' => ['required_with:custom-times', Rule::in(array_keys(config('goals.time_periods'))), ],
            'habit-strength' => 'required_with:habit|numeric|min:1|max:100',
            'start-date' => 'date_format:Y-m-d|before:end-date',
            'end-date' => 'required_with:habit-strength,start-date|date_format:Y-m-d',
            'goal-image' => 'image',
            'reason' => 'required|string',
            'show-todo-days-before' => 'required_with:show-todo|numeric|min:0|max:60',
            'notes' => 'nullable',
        ];
    }
}
