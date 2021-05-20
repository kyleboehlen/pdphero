<?php

namespace App\View\Components\Affirmations;

use Illuminate\View\Component;

class Form extends Component
{
    public $affirmation;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($affirmation = null)
    {
        $this->affirmation = $affirmation;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.affirmations.form');
    }
}
