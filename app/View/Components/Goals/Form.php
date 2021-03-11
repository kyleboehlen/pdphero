<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Goal\Type;

// Models
use App\Models\Goal\Goal;
use App\Models\Goal\GoalType;
use App\Models\Habits\Habits;


class Form extends Component
{
    // For storing goal types constant class
    public $type;

    // For storing what type we're creating/editing
    public $type_id;
    public $type_name; // For header

    // For storing the goal we're editing, if we are editing
    public $edit_goal;

    // For storing the future goal we're converting, if that's the case
    public $future_goal;

    // For storing the parent goal we're creating a sub goal for, if that's the case
    public $parent_goal;

    // For storing the user's goal categories
    public $categories;

    // For storing the user's habits if it's a habit based goal
    public $habits;

    // For storing ad hoc periods if the goal is action plan ad hoc
    public $ad_hoc_periods;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($goal = null, $type = null, $future = null, $parent = null)
    {
        $this->type = Type::class;
        
        // If a goal is being passed, assign that as the goal we're editing and get the type_id from it
        if(!is_null($goal))
        {
            $this->edit_goal = $goal;
            $this->type_id = $goal->type_id;
            $this->type_name = null;
        }
        else // A type has to be passed for creation
        {
            $this->type_id = $type;
            $this->type_name = GoalType::find($type)->name;
        }

        // Assign parent/future, they'll just be null for edits or if not relevant for creation
        if(!is_null($future))
        {
            $this->future_goal = Goal::where('uuid', $future)->first();
        }
        else
        {
            $this->future_goal = $future;
        }

        if(!is_null($parent))
        {
            $this->parent_goal = Goal::where('uuid', $parent)->first();
        }
        else
        {
            $this->parent_goal = $parent;
        }

        // Get user's categories
        $user = \Auth::user();
        $this->categories = $user->goalCategories;

        // Get habits if it's a habit based goal
        if($this->type_id == Type::HABIT_BASED)
        {
            $this->habits = $user->habits;

            // Todo: build habit quickest date can achieve 100 percent
        }
        elseif($this->type_id == Type::ACTION_AD_HOC)
        {
            $this->ad_hoc_periods = config('goals.ad_hoc_periods');
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.form');
    }
}
