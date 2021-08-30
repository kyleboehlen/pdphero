<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class Reminder extends Component
{
    // To hold the reminder
    public $reminder;

    // To hold action item
    public $action_item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($reminder = null, $item = null)
    {
        $this->reminder = $reminder;
        $this->action_item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.goals.reminder');
    }
}
