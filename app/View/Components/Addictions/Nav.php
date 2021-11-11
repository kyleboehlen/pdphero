<?php

namespace App\View\Components\Addictions;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Addiction\Method;

class Nav extends Component
{
    // And array of which addiction menu options to show
    public $show;

    // If Nav is showing up on a details page we pass the addiction obj
    public $addiction;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'list', $addiction = null)
    {
        $this->show = explode('|', $show);
        $this->addiction = $addiction;

        // Check if we're showing the moderated usage or relapse form
        if(in_array('usage', $this->show))
        {
            if($addiction->method_id == Method::MODERATION)
            {
                $usage = $addiction->usage()->get()->count();
    
                if($usage >= $addiction->moderated_amount)
                {
                    array_push($this->show, 'relapse-form');
                }
                else
                {
                    array_push($this->show, 'moderate');
                }
            }
            else
            {
                array_push($this->show, 'relapse-form');
            }
        }

        // See if we should show the relapse timeline option
        if(in_array('relapse-timeline', $this->show))
        {
            $addiction->load('relapses');

            if($addiction->relapses->count() == 0)
            {
                $key = array_search('relapse-timeline', $this->show);
                unset($this->show[$key]);
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
        return view('components.addictions.nav');
    }
}
