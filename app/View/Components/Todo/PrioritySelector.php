<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;
use App\Helpers\Constants\ToDo\Priority;

class PrioritySelector extends Component
{
    // Determines which priority is selected
    public $selected;

    // Sets the priority constants
    public $priorities;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selected = 0)
    {
        $this->selected = $selected;
        $this->priorities = config('todo.priorities');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.todo.priority-selector');
    }
}
