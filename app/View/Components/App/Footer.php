<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class Footer extends Component
{
    // Which icon is currently active
    public $highlight;

    // Whether or not to show the home navigation icon
    public $home;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($highlight = 'none')
    {
        $this->highlight = $highlight;

        $user = \Auth::user();

        $this->home = $user->getSettingValue(Setting::SHOW_HOME_ICON);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.app.footer');
    }
}
