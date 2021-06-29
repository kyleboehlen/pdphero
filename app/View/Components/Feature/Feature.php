<?php

namespace App\View\Components\Feature;

use Illuminate\View\Component;

class Feature extends Component
{
    // For holding the feature maybe? ffs
    public $feature;

    // Style class based on vote
    public $class;

    // Determines whether or not the feature is checked based on whether there is a vote from that user or nah
    public $checked;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($feature)
    {
        $this->feature = $feature;

        // Instantiate
        $this->class = 'dont-care';
        $this->checked = false;

        // Vote search
        $user_id = \Auth::user()->id;
        $vote = $feature->votes->firstWhere('user_id', $user_id);
        if(!is_null($vote))
        {
            if($vote->value > 0)
            {
                $this->class = 'want';
            }
            elseif($vote->value < 0)
            {
                $this->class = 'dont-want';
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
        return view('components.feature.feature');
    }
}
