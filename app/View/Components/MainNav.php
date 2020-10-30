<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MainNav extends Component
{
    /**
     * Determines which nav menu options to show
     *
     * @var string
     */
    public $page;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.main-nav');
    }
}
