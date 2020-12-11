<?php

namespace App\Http\Requests\Affirmations;

use Illuminate\Foundation\Http\FormRequest;

class AffirmationRequest extends FormRequest
{
    /**
     * Remove new lines from affirmation
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if($this->has('affirmation'))
        {
            $this->merge(['affirmation' => str_replace(PHP_EOL, ' ', $this->get('affirmation'))]);
        }
    }
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
            'affirmation' => 'required|string|max:255',
        ];
    }
}
