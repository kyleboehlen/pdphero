<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalType;

class CreateRequest extends FormRequest
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
        // Get user to populate parent/future goal uuids for rule::in
        $user = \Auth::user();

        // Get parent goal uuids
        $parent_goal_uuids = array();
        $parent_goals = Goal::where('user_id', $user->id)->where('type_id', Type::PARENT_GOAL)->get();
        if(!is_null($parent_goals))
        {
            $parent_goal_uuids = $parent_goals->pluck('uuid')->toArray();
        }

        // Get future goal uuids
        $future_goals = Goal::where('user_id', $user->id)->where('type_id', Type::FUTURE_GOAL)->get();
        if(!is_null($future_goals))
        {
            $future_goal_uuids = $future_goals->pluck('uuid')->toArray();
        }

        // Get goal types IDs
        $goal_type_ids = GoalType::all()->pluck('id')->toArray();

        // Return rules
        return [
            'parent-goal' => ['nullable', Rule::in($parent_goal_uuids)],
            'future-goal' => ['nullable', Rule::in($future_goal_uuids)],
            'type' => ['nullable', Rule::in($goal_type_ids)],
        ];
    }
}
