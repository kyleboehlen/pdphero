<?php

namespace App\View\Components\Profile;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class Nav extends Component
{
    // And array of which profile menu options to show
    public $show;

    // Whether or not the user has verified their SMS number
    public $sms_verified;

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

        // Determine wording for sms number nav option
        if(in_array('sms-number', $this->show))
        {
            $user = \Auth::user();

            $this->sms_verified = !is_null($user->sms_verified_at);
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
