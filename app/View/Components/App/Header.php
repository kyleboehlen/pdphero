<?php

namespace App\View\Components\App;

use Illuminate\View\Component;

class Header extends Component
{
    public $title;

    // Icon to display upper right
    public $icon;

    // Route for the upper right icon
    public $route;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $icon = null, $route = null)
    {
        // Set title
        $this->title = $title;

        // Set icon
        $this->icon = $icon ?? 'profile';
        $this->route = $route ?? 'profile';

        // Set profile picture if user has one
        $user = \Auth::user();
        if($this->icon == 'profile' && !is_null($user->profile_picture))
        {
            $this->icon = "profile-pictures/$user->profile_picture?updated=$user->updated_at";
        }
        else
        {
            // Set icon link properly
            $this->icon = "$this->icon-black";
            $this->icon = "icons/$this->icon.png";
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.app.header');
    }
}
