<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;

class TotalsSummary extends Component
{
    // Month totals are for
    public $month;

    // Count vars
    public $todo_count;
    public $habit_count;
    public $goal_count;
    public $action_item_count;
    public $journal_entry_count;
    public $affirmations_count;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($array, $month)
    {
        $this->month = $month;
        $this->todo_count = array_sum(array_column($array, 'todo_count'));
        $this->habit_count = array_sum(array_column($array, 'habit_count'));
        $this->goal_count = array_sum(array_column($array, 'goal_count'));
        $this->action_item_count = array_sum(array_column($array, 'action_item_count'));
        $this->journal_entry_count = array_sum(array_column($array, 'journal_entry_count'));
        $this->affirmations_count = array_sum(array_column($array, 'affirmations_count'));
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if( $this->todo_count > 0 ||
            $this->habit_count > 0 ||
            $this->goal_count > 0 ||
            $this->action_item_count > 0 ||
            $this->journal_entry_count > 0 ||
            $this->affirmations_count > 0)
        {
            return view('components.journal.totals-summary');
        }
        
        return;
    }
}
