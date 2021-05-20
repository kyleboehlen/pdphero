<?php

namespace App\Rules\Habits;

use Illuminate\Contracts\Validation\Rule;

class DaysOfWeekArray implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // Start with array check
        if(!is_array($value))
        {
            return false;
        }
        elseif(count($value) != count(array_unique($value))) // Verify no duplicates
        {
            return false;
        }

        // Verify each value
        foreach($value as $day)
        {
            // Not a valid 'w' php day
            if($day < 0 || $day > 6)
            {
                return false;
            }
        }

        // Validated
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You need to choose at least one day of the week.';
    }
}
