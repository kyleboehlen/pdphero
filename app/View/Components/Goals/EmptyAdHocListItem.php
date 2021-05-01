<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class EmptyAdHocListItem extends Component
{
    // For holding the goal... yeah, sldkjflsf
    public $goal;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($goal)
    {
        $this->goal = $goal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.empty-ad-hoc-list-item');
    }
}
