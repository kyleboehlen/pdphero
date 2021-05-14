<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;


class TimelineItem extends Component
{
    // For holding the time for the time label
    public $time;

    // For holding the content of the completed item
    public $class;
    public $bold_content;
    public $italic_content;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->time = $item->display_time;

        // And set the content
        if($item instanceof \App\Models\ToDo\ToDo)
        {
            $this->class = 'summary-show-todo-item';
            $this->bold_content = 'Completed To-Do:';
            $this->italic_content = $item->title;
        }
        elseif($item instanceof \App\Models\Goal\Goal)
        {
            $this->class = 'summary-show-goal';
            $this->bold_content = 'Achieved Goal:';
            $this->italic_content = $item->name;
        }
        elseif($item instanceof \App\Models\Goal\GoalActionItem)
        {
            $this->class = 'summary-show-action-item';
            $this->bold_content = 'Achieved Action Item:';
            $this->italic_content = $item->name;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.journal.timeline-item');
    }
}
