<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // About
        View::composer('about', 'App\View\Composers\AboutComposer');

        // About
        View::composer('affirmations.*', 'App\View\Composers\AffirmationsComposer');

        // Auth
        View::composer('auth.*', 'App\View\Composers\AuthComposer');

        // Habits
        View::composer('habits.*', 'App\View\Composers\HabitsComposer');

        // Profile
        View::composer('profile.*', 'App\View\Composers\ProfileComposer');
        View::composer('profile.edit.settings', 'App\View\Composers\SettingsComposer');

        // ToDo
        View::composer('todo.*', 'App\View\Composers\ToDoComposer');
    }
}