<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class AdHocListItem extends Component
{
    // Holds ad hoc action item
    public $ad_hoc_item;

    // Holds the goal for bucketlist ad hoc items
    public $goal;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item, $goal = null)
    {
        $this->goal = $goal;
        $this->ad_hoc_item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.ad-hoc-list-item');
    }
}
