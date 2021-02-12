<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class Nav extends Component
{
    // And array of which profile menu options to show
    public $show;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($show = 'list')
    {
        $this->show = explode('|', $show);

        // Remove show-rules if it's turned off for the user
        if(in_array('edit-rules', $this->show))
        {
            $user = \Auth::user();

            if(!$user->getSettingValue(Setting::PROFILE_SHOW_RULES))
            {
                $key = array_search('edit-rules', $this->show);
                unset($this->show[$key]);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.profile.nav');
    }
}
