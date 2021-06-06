<?php

namespace App\View\Composers;

use Illuminate\View\View;

class FeatureVoteComposer
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
            'stylesheet' => 'feature',
        ]);
    }
}