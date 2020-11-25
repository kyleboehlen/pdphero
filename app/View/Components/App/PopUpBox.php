<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

class PopUpBox extends Component
{
    // Determines title and class
    public $title;

    // Passthrough for message
    public $message;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.app.pop-up-box');
    }
}
