<?php

namespace App\Rules\Goal;

use Illuminate\Contracts\Validation\Rule;

class Category implements Rule
{
    public $category_uuids;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $user = \Auth::user();
        $this->category_uuids = $user->goalCategories->pluck('uuid')->toArray();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // No category
        if($value === 'no-category')
        {
            return true;
        }

        // Return whether or not the category uuid is the users
        return in_array($value, $this->category_uuids);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid goal category';
    }
}
