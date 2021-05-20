<?php

namespace App\View\Composers;

use Illuminate\View\View;

class ProfileComposer
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
            'stylesheet' => 'profile',
            'user' => $user,
        ]);
    }
}