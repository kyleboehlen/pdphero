<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Goal\Type;

class ActionItemForm extends Component
{
    // To hold the action item we're editing
    public $action_item;

    // For holding the goal the action item is for
    public $goal;

    // For holding goal type constant class
    public $goal_type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($action_item = null, $goal = null)
    {
        $this->action_item = $action_item;

        if(!is_null($goal))
        {
            $this->goal = $goal;
        }
        else
        {
            $this->goal = $action_item->goal;
        }

        $this->goal_type = Type::class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.action-item-form');
    }
}
