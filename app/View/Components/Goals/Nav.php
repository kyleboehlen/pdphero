<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which goal menu options to show
    public $show;

    // If Nav is showing up on a goal view/edit page the goal is passed
    public $goal;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $goal = null)
    {
        $this->show = explode('|', $show);
        $this->goal = $goal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.nav');
    }
}
