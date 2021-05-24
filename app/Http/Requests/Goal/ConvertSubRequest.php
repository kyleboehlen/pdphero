<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;

class ConvertSubRequest extends FormRequest
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

        // Get parent goal uuids
        $parent_goal_uuids = array();
        $parent_goals = Goal::where('user_id', $user->id)->where('type_id', Type::PARENT_GOAL)->get();
        if(!is_null($parent_goals))
        {
            $parent_goal_uuids = $parent_goals->pluck('uuid')->toArray();
        }

        return [
            'parent-goal' => [Rule::in($parent_goal_uuids), ],
        ];
    }
}
