<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

// Models
use App\Models\ToDo\ToDo;

class Item extends Component
{
    public $item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(ToDo $item)
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
        return view('components.todo.item');
    }
}
