<?php

namespace App\Http\Requests\Journal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // Get category uuids
        $user = \Auth::user();
        $category_uuids = $user->journalCategories()->get()->pluck('uuid')->toArray();

        return [
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::in($category_uuids), ],
            'body' => 'required',
        ];
    }
}
