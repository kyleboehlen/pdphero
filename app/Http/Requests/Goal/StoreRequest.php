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
        // Get user
        $user = \Auth::user();

        // Get habit uuids for user
        $habits_uuids = $user->habits->pluck('uuid')->toArray();

        // Get parent goal uuids
        $parent_goal_uuids = array();
        $parent_goals = Goal::where('user_id', $user->id)->where('type_id', Type::PARENT_GOAL)->get();
        if(!is_null($parent_goals))
        {
            $parent_goal_uuids = $parent_goals->pluck('uuid')->toArray();
        }

        // Get future goal uuids
        $future_goal_uuids = array();
        $future_goals = Goal::where('user_id', $user->id)->where('type_id', Type::FUTURE_GOAL)->get();
        if(!is_null($future_goals))
        {
            $future_goal_uuids = $future_goals->pluck('uuid')->toArray();
        }

        return [
            'type' => ['required', Rule::in(array_keys(config('goals.types'))), ],
            'parent-goal' => [Rule::in($parent_goal_uuids), ],
            'future-goal' => [Rule::in($future_goal_uuids), ],
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
