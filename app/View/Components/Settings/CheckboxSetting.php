<?php

namespace App\View\Components\Settings;

use Illuminate\View\Component;

// Constants
use App\Helpers\Constants\User\Setting;

class CheckboxSetting extends Component
{
    // Holds the ID of the corresponding setting
    public $settings_id;

    // The text to be displayed next to the checkbox
    public $text;

    // Determines whether the checkbox is checked or not
    public $checked;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $text)
    {
        $this->settings_id = $id;

        $this->text = $text;

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
        return view('components.settings.checkbox-setting');
    }
}
