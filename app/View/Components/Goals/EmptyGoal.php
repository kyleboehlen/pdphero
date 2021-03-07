<?php

namespace App\View\Components\Goals;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\Goal\Type;

class EmptyGoal extends Component
{
    // Scope hides progress/status and uses a different default image
    public $scope;

    // For holding goal type constants
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($scope = 'active')
    {
        $this->scope = $scope;
        $this->type = Type::class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.goals.empty-goal');
    }
}
