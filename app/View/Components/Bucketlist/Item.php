<?php

namespace App\View\Components\Bucketlist;

use Illuminate\View\Component;

class Item extends Component
{
    // To hold the bucketlist item
    public $item;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bucketlist.item');
    }
}
