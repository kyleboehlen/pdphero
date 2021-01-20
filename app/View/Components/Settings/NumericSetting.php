<?php

namespace App\View\Components\Settings;

use Illuminate\View\Component;

class NumericSetting extends Component
{
    // Holds the ID of the corresponding setting
    public $settings_id;

    // The text to be displayed before the number input
    public $text_part_one;

    // The text to be displayed after the number input
    public $text_part_two;

    // Holds min/max values allowed
    public $min;
    public $max;

    // Holds current setting value
    public $current_value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $text, $min = 0, $max = 100)
    {
        $this->settings_id = $id;

        // Split text
        $text_array = explode('|', $text);
        $this->text_part_one = $text_array[0];
        $this->text_part_two = $text_array[1];

        // Set min/max
        $this->min = $min;
        $this->max = $max;

        // Get user and determine the current setting
        $user = \Auth::user();
        $this->current_value = $user->getSettingValue($this->settings_id);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.settings.numeric-setting');
    }
}
