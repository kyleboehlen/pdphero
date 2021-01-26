<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

class Form extends Component
{
    // Determines the item to edit if that is the action
    public $item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item = null)
    {
        $this->item = $item;
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
