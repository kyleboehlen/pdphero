<?php

namespace App\View\Components\Journal;

use Illuminate\View\Component;
use Carbon\Carbon;

class Nav extends Component
{
    // And array of which journal menu options to show
    public $show;

    // If Nav is showing up on an entry view details/edit page, store the relevant entry here
    public $journal_entry;

    // For redirecting back to routes properly
    public $date;
    public $month;
    public $year;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $entry = null, $date = null, $todo = null)
    {
        $this->show = explode('|', $show);
        $this->journal_entry = $entry;
        

        // Handles parsing and creating back route params
        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver';
        if(in_array('back', $this->show) && (!is_null($date) || !is_null($entry) || !is_null($todo)))
        {
            if(!is_null($date))
            {
                $carbon = Carbon::parse($date);
            }
            elseif(!is_null($entry))
            {
                $carbon = Carbon::parse($entry->created_at)->setTimezone($timezone);
            }
            else
            {
                $carbon = Carbon::parse($todo->updated_at)->setTimezone($timezone);
            }

            $this->month = strtolower($carbon->format('F'));
            $this->year = $carbon->format('Y');
        }
        else
        {
            $this->month = null;
            $this->year = null;
        }

        if(in_array('back-day', $this->show) && (!is_null($entry) || !is_null($todo)))
        {
            if(!is_null($entry))
            {
                $carbon = Carbon::parse($entry->created_at);
            }
            else
            {
                $carbon = Carbon::parse($todo->updated_at);
            }

            $carbon->setTimezone($timezone);
            $this->date = $carbon->format('Y-m-d');
        }
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
