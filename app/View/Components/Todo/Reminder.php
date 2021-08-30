<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

class Reminder extends Component
{
    // To hold the reminder
    public $reminder;

    // To hold todo item
    public $todo;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($reminder = null, $todo = null)
    {
        $this->reminder = $reminder;
        $this->todo = $todo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.todo.reminder');
    }
}
