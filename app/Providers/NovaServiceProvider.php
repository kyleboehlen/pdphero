<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

// Metrics
use App\Nova\Metrics\AchievedActionItems;
use App\Nova\Metrics\AchievedGoals;
use App\Nova\Metrics\ActiveUsers;
use App\Nova\Metrics\AffirmationsRead;
use App\Nova\Metrics\AverageHabitStrength;
use App\Nova\Metrics\AverageGoalProgress;
use App\Nova\Metrics\CompletedToDos;
use App\Nova\Metrics\HabitsPerformed;
use App\Nova\Metrics\NewActionItems;
use App\Nova\Metrics\NewAffirmations;
use App\Nova\Metrics\NewGoals;
use App\Nova\Metrics\NewHabits;
use App\Nova\Metrics\NewJournalEntries;
use App\Nova\Metrics\NewToDos;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\UsersPerformedHabits;
use App\Nova\Metrics\UsersReadAffirmations;

// Tools
use Dniccum\CustomEmailSender\CustomEmailSender;
use KABBOUCHI\LogsTool\LogsTool;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()->withAuthenticationRoutes()->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, config('nova.gate_emails'));
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            // Users
            new NewUsers,
            new ActiveUsers,

            // To-Dos
            new NewToDos,
            new CompletedToDos,

            // Habits
            new NewHabits,
            new UsersPerformedHabits,
            new HabitsPerformed,
            new AverageHabitStrength,

            // Journal Entries
            new NewJournalEntries,

            // Affirmations
            new NewAffirmations,
            new UsersReadAffirmations,
            new AffirmationsRead,

            // Goals/ActionItems
            new NewGoals,
            new AchievedGoals,
            new AverageGoalProgress,
            new NewActionItems,
            new AchievedActionItems,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [new CustomEmailSender(), new LogsTool(), ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
