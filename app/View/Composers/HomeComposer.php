<?php

namespace App\View\Composers;

use Illuminate\View\View;

// Models
use App\Models\Home\Home;

class HomeComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = \Auth::user();

        $view->with([
            'home_icons' => Home::all(),
            'hide_array' => $user->hideHomeArray(),
            'stylesheet' => 'home',
        ]);
    }
}