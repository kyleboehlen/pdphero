<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;
use Carbon\Carbon;

class TimelineEntry extends Component
{
    // For holidng the journal entry
    public $journal_entry;

    // For creating a search body
    public $before_body;
    public $matching_text;
    public $after_body;

    // For creating a search title
    public $before_title;
    public $matching_title;
    public $after_title;

    // Mood class
    public $mood;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($entry, $search = null)
    {
        if(!is_null($search))
        {
            // Set display timestamp
            $user = \Auth::user();
            $timezone = $user->timezone ?? 'America/Denver';
            $entry->display_time = Carbon::parse($entry->created_at)->setTimezone($timezone)->format('n/j/y g:i A');

            // Match Body
            $length = strlen($search);
            $index = stripos($entry->body, $search);
            if($index !== false) // Search string doesn't match body
            {
                $chars_before = 25;
                if($chars_before >= $index)
                {
                    $start_index = 0;
                    $start_length = $index;
                }
                else
                {
                    $start_index = $index - $chars_before;
                    $start_length = $chars_before;
                }
                $this->before_body = substr($entry->body, $start_index, $start_length);
                $this->matching_text = substr($entry->body, $index, $length);
                $this->after_body = substr($entry->body, $index + $length);
            }
            else
            {
                $this->before_body = null;
                $this->matching_text = null;
                $this->after_body = null;
            }

            // Search title
            $index = stripos($entry->title, $search);
            if($index !== false) // Search string doesn't match title
            {
                $index = stripos($entry->title, $search);
                $this->before_title = substr($entry->title, 0, $index);
                $this->matching_title = substr($entry->title, $index, $length);
                $this->after_title = substr($entry->title, $index + $length);
            }
            else
            {
                $this->before_title = null;
                $this->matching_title = null;
                $this->after_title = null;
            }
        }

        $this->journal_entry = $entry;
        $this->mood = strtolower(config('journal.moods')[$entry->mood_id]['name']);
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
