<?php

namespace App\View\Components\Todo;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\ToDo\Type;

class Nav extends Component
{
    // And array of which to-do menu options to show
    public $show;

    // If Nav is showing up on an item page the item is passed
    public $item;

    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'list', $item = null)
    {
        $this->show = explode('|', $show);
        $this->item = $item;
        $this->type = Type::class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.todo.nav');
    }
}
