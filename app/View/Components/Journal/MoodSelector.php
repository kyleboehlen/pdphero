<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;

class MoodSelector extends Component
{
    // Determines which mood is selected
    public $selected;

    // Sets the moods constant
    public $moods;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($selected = 0)
    {
        $this->selected = $selected;
        $this->moods = config('journal.moods');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.mood-selector');
    }
}
