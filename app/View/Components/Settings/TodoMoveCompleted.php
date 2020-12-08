<?php

namespace App\View\Components\Settings;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class TodoMoveCompleted extends Component
{
    // Holds the ID of the corresponding setting
    public $settings_id;

    // Determines whether the checkbox is checked or not
    public $checked;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->settings_id = Setting::TODO_MOVE_COMPLETED;

        // Get user and determine if this setting is checked or nah
        $user = \Auth::user();
        $this->checked = $user->getSettingValue($this->settings_id);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.settings.todo-move-completed');
    }
}
