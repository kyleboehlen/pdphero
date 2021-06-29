<?php

namespace App\View\Components\Feature;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which nav options to show
    public $show;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = '')
    {
        $this->show = explode('|', $show);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.feature.nav');
    }
}
