<?php

namespace App\View\Components\Settings;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class TodoShowCompletedFor extends Component
{
    // Holds the ID of the corresponding setting
    public $settings_id;

    // Current value for the setting in hours
    public $hours;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->settings_id = Setting::TODO_SHOW_COMPLETED_FOR;

        // Get user and current settings value
        $user = \Auth::user();
        $this->hours = $user->getSettingValue($this->settings_id);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.settings.todo-show-completed-for');
    }
}
