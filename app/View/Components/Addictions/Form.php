<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;
use Carbon\Carbon;

// Constants
use App\Helpers\Constants\Addiction\Method;

// Models
use App\Models\Addictions\AddictionMethod;

class Form extends Component
{
    public $addiction;
    public $carbon_now;
    public $carbon_start;
    public $methods;
    public $moderation;
    public $moderation_periods;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($addiction = null)
    {
        $this->addiction = $addiction;
        $this->methods = AddictionMethod::all();
        $this->moderation = Method::MODERATION;
        $this->moderation_periods = config('addictions.date_formats');

        $user = \Auth::user();
        $timezone = $user->timezone ?? 'America/Denver';

        $this->carbon_now = new Carbon('now', $timezone);

        if(!is_null($addiction))
        {
            $this->carbon_start = Carbon::createFromFormat('Y-m-d H:i:s', $addiction->created_at, 'UTC')->setTimezone($timezone);

            if(!is_null($addiction->start_date))
            {
                $carbon_start = Carbon::createFromFormat('Y-m-d', $addiction->start_date, $timezone);
                $this->carbon_start->year = $carbon_start->year;
                $this->carbon_start->month = $carbon_start->month;
                $this->carbon_start->day = $carbon_start->day;
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.addictions.form');
    }
}
