<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;
use Carbon\Carbon;

class TimelineEntry extends Component
{
    // For holidng the journal entry
    public $journal_entry;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($entry)
    {
        $this->journal_entry = $entry;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.timeline-entry');
    }
}
