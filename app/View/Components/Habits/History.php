<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;

class History extends Component
{
    public $habit;
    public $habit_history_array;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($habit, $offset = 0, $format = 'D')
    {
        $this->habit = $habit;
        $this->habit_history_array = $this->habit->getHistoryArray($offset, $format);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.habits.history');
    }
}
