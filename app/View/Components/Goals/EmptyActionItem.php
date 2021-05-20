<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class EmptyActionItem extends Component
{
    // For holding the goal we're creating an action item for
    public $goal;

    // For holding route for links
    public $route;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($goal, $route = 'goals.create.action-item')
    {
        $this->goal = $goal;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.empty-action-item');
    }
}
