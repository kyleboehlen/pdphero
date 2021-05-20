<?php

namespace App\View\Composers;

use Illuminate\View\View;

// Constants
use App\Helpers\Constants\User\Setting;

class SettingsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'setting' => Setting::class,
        ]);
    }
}