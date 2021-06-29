<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;

class TransferAdHocRequest extends FormRequest
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

        // Get ad hoc uuids
        $ad_hoc_goal_uuids = array();
        $ad_hoc_goals = Goal::where('user_id', $user->id)->where('type_id', Type::ACTION_AD_HOC)->get();
        if(!is_null($ad_hoc_goals))
        {
            $ad_hoc_goal_uuids = $ad_hoc_goals->pluck('uuid')->toArray();
        }

        return [
            'ad-hoc-goal' => [Rule::in($ad_hoc_goal_uuids), ],
        ];
    }
}
