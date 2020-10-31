<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MainHeader extends Component
{
    public $hide_profile_link;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($hide_profile_link = false)
    {
        $this->hide_profile_link = $hide_profile_link;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.main-header');
    }
}
