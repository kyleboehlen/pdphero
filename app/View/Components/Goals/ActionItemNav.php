<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class ActionItemNav extends Component
{
    // And array of which goal action item menu options to show
    public $show;

    // Action item to use when showing action item related nav items, edit/delete/etc
    public $action_item;

    // For holding the goal if the action item hasn't been created
    public $goal;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $item = null, $goal = null)
    {
        $this->show = explode('|', $show);
        $this->action_item = $item;

        if(!is_null($goal))
        {
            $this->goal = $goal;
        }
        else
        {
            $this->goal = $item->goal;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.action-item-nav');
    }
}
