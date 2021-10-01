<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which addiction menu options to show
    public $show;

    // If Nav is showing up on a details page we pass the addiction obj
    public $addiction;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'list', $addiction = null)
    {
        $this->show = explode('|', $show);
        $this->addiction = $addiction;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.addictions.nav');
    }
}
