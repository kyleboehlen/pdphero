<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

// Constants
use App\Helpers\Constants\Habits\Type;

class Form extends Component
{
    // Holds the carbon period to iterate through for the days of week input
    public $carbon_period;

    // Holds the habit we're editing, if we're editing
    public $habit;

    // Habit types
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($habit = null)
    {
        // Assign habit
        $this->habit = $habit;
        
        // Create carbon period for days of week input
        $user = \Auth::user(); 
        $timezone = $user->timezone ?? config('app.timezone');
        $now = new Carbon('now', $timezone);
        $this->carbon_period = new CarbonPeriod(
            (clone $now)->startOfWeek()->format('Y-m-d'),
            (clone $now)->endOfWeek()->format('Y-m-d'),
        );

        // Habit types class
        $this->type = Type::class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.habits.form');
    }
}
