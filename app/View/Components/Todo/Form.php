<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\ToDo\Type;

class Form extends Component
{
    // Determines the item to edit if that is the action
    public $item;

    // For holding the todo types constant class
    public $type;

    // For holding which type of todo item we're creating
    public $create_type;

    // For holding the user's habits if we're creating a singular habit item
    public $habits;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item = null, $create = Type::TODO_ITEM)
    {
        $this->item = $item;
        $this->type = Type::class;
        $this->create_type = $create;

        // Get habits if we're creating a singular habit todo item
        if($this->create_type == Type::SINGULAR_HABIT_ITEM)
        {
            $user = \Auth::user();
            $this->habits = $user->habits;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.todo.form');
    }
}
