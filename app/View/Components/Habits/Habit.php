<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Habits\Type;
use App\Helpers\Constants\User\Setting;

// Models
use App\Models\Habits\Habits;

class Habit extends Component
{
    public $habit;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Habits $habit)
    {
        $this->habit = $habit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        // Check if habit is an afirmations habit
        if($this->habit->type_id == Type::AFFIRMATIONS_HABIT)
        {
            $user = \Auth::user(); // Get user and
            // Check if user has affirmations habit turned on
            if(!$user->getSettingValue(Setting::HABITS_SHOW_AFFIRMATIONS_HABIT))
            {
                return null; // Dont render view
            }
        }

        return view('components.habits.habit');
    }
}
