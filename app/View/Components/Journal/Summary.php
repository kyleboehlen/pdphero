<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;

class Summary extends Component
{
    // Date of the summary
    public $display_date;
    public $route_date;

    // Count vars
    public $todo_count;
    public $habit_count;
    public $goal_count;
    public $action_item_count;
    public $journal_entry_count;
    public $affirmations_count;
    public $bucketlist_item_count;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->display_date = $array['display_date'];
        $this->route_date = $array['route_date'];
        $this->todo_count = $array['todo_count'];
        $this->habit_count = $array['habit_count'];
        $this->goal_count = $array['goal_count'];
        $this->action_item_count = $array['action_item_count'];
        $this->journal_entry_count = $array['journal_entry_count'];
        $this->affirmations_count = $array['affirmations_count'];
        $this->bucketlist_item_count = $array['bucketlist_item_count'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.summary');
    }
}
