<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;

class ElapsedTimer extends Component
{
    public $elapsed_years;
    public $elapsed_months;
    public $elapsed_days;
    public $elapsed_hours;
    public $elapsed_minutes;
    public $elapsed_seconds;
    public $uuid;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($addiction)
    {
        $elapsed_carbon = $addiction->getElapsedCarbon();
        $this->elapsed_years = $elapsed_carbon->y;
        $this->elapsed_months = $elapsed_carbon->m;
        $this->elapsed_days = $elapsed_carbon->d;
        $this->elapsed_hours = $elapsed_carbon->h;
        $this->elapsed_minutes = $elapsed_carbon->i;
        $this->elapsed_seconds = $elapsed_carbon->s;
        $this->uuid = $addiction->uuid;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.addictions.elapsed-timer');
    }
}
