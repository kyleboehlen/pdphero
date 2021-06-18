<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Goal\Type;

class Nav extends Component
{
    // And array of which goal menu options to show
    public $show;

    // If Nav is showing up on a goal view/edit page the goal is passed
    public $goal;

    // For keeping the selected scope when clicking back to goals
    public $scope;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show, $goal = null)
    {
        $this->show = explode('|', $show);
        $this->goal = $goal;

        if(!is_null($goal))
        {
            if($goal->achieved)
            {
                $this->scope = 'achieved';
            }
            elseif($goal->type_id == Type::FUTURE_GOAL)
            {
                $this->scope = 'future';
            }
            else
            {
                $this->scope = 'active';
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.nav');
    }
}
