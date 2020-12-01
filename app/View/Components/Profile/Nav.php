<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which profile menu options to show
    public $show;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'list')
    {
        $this->show = explode('|', $show);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.profile.nav');
    }
}
