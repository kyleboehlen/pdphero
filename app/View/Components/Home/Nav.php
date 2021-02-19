<?php

namespace App\View\Components\Home;

use Illuminate\View\Component;

class Nav extends Component
{
    public $show;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'edit')
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
        return view('components.home.nav');
    }
}
