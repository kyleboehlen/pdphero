<?php

namespace App\Http\Requests\ToDo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHabitRequest extends FormRequest
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
        // Get habit uuids
        $user = \Auth::user();
        $uuids = $user->habits->pluck('uuid')->toArray();

        // Get category uuids
        $category_uuids = $user->todoCategories()->get()->pluck('uuid')->toArray();
        array_push($category_uuids, 'no-category');

        return [
            'habit' => ['required', Rule::in($uuids)],
            'category' => ['required', Rule::in($category_uuids), ],
            'notes' => 'nullable',
        ];
    }
}
