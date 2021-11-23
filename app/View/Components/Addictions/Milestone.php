<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;

class Milestone extends Component
{
    public $milestone;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($milestone)
    {
        $this->milestone = $milestone;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.addictions.milestone');
    }
}
