<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;

class Nav extends Component
{
    // And array of which journal menu options to show
    public $show;

    // If Nav is showing up on an entry view details/edit page, store the relevant entry here
    public $journal_entry;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $entry = null)
    {
        $this->show = explode('|', $show);
        $this->journal_entry = $entry;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.nav');
    }
}
