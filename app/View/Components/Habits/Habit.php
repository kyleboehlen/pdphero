<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Habits\Type;

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
        return view('components.habits.habit');
    }
}
