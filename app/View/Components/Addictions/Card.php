<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Addiction\Method;

class Card extends Component
{
    public $addiction;
    public $method;
    public $milestone_name;
    public $usage;
    public $usage_color;

    public $elapsed_years;
    public $elapsed_months;
    public $elapsed_days;
    public $elapsed_hours;
    public $elapsed_minutes;
    public $elapsed_seconds;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($addiction)
    {
        $this->addiction = $addiction;
        $this->method = Method::class;

        if($addiction->method_id == Method::MODERATION)
        {
            $this->usage = $addiction->usage()->get()->count();

            if($this->usage == 0)
            {
                $this->usage_color = 'green';
            }
            elseif($this->usage >= $addiction->moderated_amount)
            {
                $this->usage_color = 'red';
            }
            else
            {
                $this->usage_color = 'yellow';
            }
        }
        elseif($addiction->method_id == Method::ABSTINENCE)
        {
            $acheieved_milestones = $addiction->reachedMilestones()->get();
            
            if($acheieved_milestones->count() == 0)
            {
                $this->milestone_name = null;
            }
            else
            {
                $this->milestone_name = $acheieved_milestones->first()->name;
            }
        }

        $elapsed_carbon = $addiction->getElapsedCarbon();
        $this->elapsed_years = $elapsed_carbon->y;
        $this->elapsed_months = $elapsed_carbon->m;
        $this->elapsed_days = $elapsed_carbon->d;
        $this->elapsed_hours = $elapsed_carbon->h;
        $this->elapsed_minutes = $elapsed_carbon->i;
        $this->elapsed_seconds = $elapsed_carbon->s;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.addictions.card');
    }
}
