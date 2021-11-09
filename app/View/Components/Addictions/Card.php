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
                $this->milestone_name = $acheieved_milestones->last()->name;
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
        return view('components.addictions.card');
    }
}
