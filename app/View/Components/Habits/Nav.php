<?php

namespace App\View\Components\Habits;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which habits menu options to show
    public $show;

    // If Nav is showing up on a habit view/edit page the habit is passed
    public $habit;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $habit = null)
    {
        $this->show = explode('|', $show);
        $this->habit = $habit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.habits.nav');
    }
}
