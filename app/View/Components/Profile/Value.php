<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;

class Value extends Component
{
    // Holds the, well, value of the value...
    public $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.profile.value');
    }
}
