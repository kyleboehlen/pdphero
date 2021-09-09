<?php

namespace App\View\Components\Bucketlist;

use Illuminate\View\Component;

class Form extends Component
{
    // Determines the item to edit if that is the action
    public $item;

    // For holding a user's bucketlist categories
    public $categories;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item = null)
    {
        // Assign item
        $this->item = $item;

        // Get todo categories
        $user = \Auth::user();
        $this->categories = $user->bucketlistCategories()->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bucketlist.form');
    }
}
