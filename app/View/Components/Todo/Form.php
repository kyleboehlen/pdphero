<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

class Form extends Component
{
    // Determines create vs edit
    public $action;

    // Determines the item to edit if that is the action
    public $item;

    // Determines what the h2 title shows
    public $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($action, $item = null)
    {
        $this->action = $action;
        $this->item = $item;
        $this->title = $title;
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
