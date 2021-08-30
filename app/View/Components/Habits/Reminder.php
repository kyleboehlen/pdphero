<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;

class Reminder extends Component
{
    // To hold the reminder
    public $reminder;

    // To hold the habit
    public $habit;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($reminder = null, $habit = null)
    {
        $this->reminder = $reminder;
        $this->habit = $habit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.habits.reminder');
    }
}
