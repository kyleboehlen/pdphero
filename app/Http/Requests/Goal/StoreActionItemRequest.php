<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

// Constants
use App\Helpers\Constants\Goal\Type;

class StoreActionItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $goal = $this->route('goal');
        
        return in_array($goal->type_id, [Type::ACTION_AD_HOC, Type::ACTION_DETAILED]);
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

        // Set base rules
        $rules = [
            'name' => 'required|string|max:255',
            'deadline' => 'date_format:Y-m-d',
            'override-show-todo' => 'required_with:show-todo',
            'show-todo' => 'required_with:show-todo-days-before',
            'show-todo-days-before' => 'required_with:show-todo|numeric|min:0|max:60',
            'notes' => 'nullable',
        ];
        
        // If it's an action detailed make deadline required
        if($this->route('goal')->type_id == Type::ACTION_DETAILED)
        {
            $rules['deadline'] = 'required|date_format:Y-m-d';
        }

        return $rules;
    }
}
