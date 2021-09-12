<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class AdHocDeadlinePopup extends Component
{
    // For the action item we're creating a deadline dialog for
    public $ad_hoc_item;
    
    // To determine redirect
    public $view_details;

    // To hold the goal we're assigning bucketlist items to
    public $goal;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item, $details = false, $goal = null)
    {
        $this->goal = $goal;
        $this->ad_hoc_item = $item;
        $this->view_details = (bool) $details;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.ad-hoc-deadline-popup');
    }
}
