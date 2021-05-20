<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;

class Rule extends Component
{
    // Holds the value of the rule
    public $rule;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rule = null)
    {
        $this->rule = $rule;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.profile.rule');
    }
}
