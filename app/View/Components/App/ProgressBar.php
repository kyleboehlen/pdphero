<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

class ProgressBar extends Component
{
    public $percent;
    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($percent)
    {
        if($percent > 100)
        {
            $this->percent = 100;
        }
        else
        {
            $this->percent = $percent;
        }

        $this->label = $percent;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.app.progress-bar');
    }
}
