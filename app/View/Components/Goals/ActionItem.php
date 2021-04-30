<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;
use Carbon\Carbon;

class ActionItem extends Component
{
    // For holding the action item
    public $action_item;

    // For holding formatted deadline
    public $deadline;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->action_item = $item;

        $this->deadline = Carbon::parse($item->deadline)->format('n/j/y');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.action-item');
    }
}
