<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;

class Form extends Component
{
    // Holding the journal entry being edited if so
    public $journal_entry;

    // User's journal entry categories
    public $categories;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($entry = null)
    {
        // Set entry
        $this->journal_entry = $entry;

        // Get journal categories
        $user = \Auth::user();
        $this->categories = $user->journalCategories()->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.form');
    }
}
