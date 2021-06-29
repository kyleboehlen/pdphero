<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

class NavLogo extends Component
{
    // It's the user, wow!
    public $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = \Auth::user();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.app.nav-logo');
    }
}
