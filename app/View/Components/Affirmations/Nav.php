<?php

namespace App\View\Components\Affirmations;

use Illuminate\View\Component;

class Nav extends Component
{
    public $affirmation;

    // and array of which to-do menu options to hide
    public $hide;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($affirmation = null, $hide = null)
    {
        $this->affirmation = $affirmation;

        if(is_null($hide))
        {
            $this->hide = array();
        }
        else
        {
            $this->hide = explode('|', $hide);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.affirmations.nav');
    }
}
