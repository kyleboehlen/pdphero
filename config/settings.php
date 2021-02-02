<?php

use App\Helpers\Constants\User\Setting;

return [
    'default' => [
        Setting::TODO_MOVE_COMPLETED => '1', // True
        Setting::TODO_SHOW_COMPLETED_FOR => '24',
        Setting::AFFIRMATIONS_SHOW_READ => '1', // True
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => '0', // False
        Setting::HABITS_DAYS_TO_DISPLAY => Setting::HABITS_ROLLING_SEVEN_DAYS,
        Setting::HABITS_START_OF_WEEK => Setting::HABITS_SUNDAY, // Like a normal person...
    ],

    'options' => [
        Setting::HABITS_DAYS_TO_DISPLAY => [
            // Key => display name
            Setting::HABITS_ROLLING_SEVEN_DAYS => 'Rolling 7 Days', 
            Setting::HABITS_CURRENT_WEEK => 'Current Week',
        ],
        Setting::HABITS_START_OF_WEEK => [
            // Key => display name
            Setting::HABITS_SUNDAY => 'Sunday', 
            Setting::HABITS_MONDAY => 'Monday',
        ],
    ],

    'seed' => [
        Setting::TODO_MOVE_COMPLETED => [
            'desc' => 'Move completed To-Do items to the bottom of the To-Do list.',
        ],
        Setting::TODO_SHOW_COMPLETED_FOR => [
            'desc' => 'Display To-Do items on the list for this many hours after they have been completed.',
        ],
        Setting::AFFIRMATIONS_SHOW_READ => [
            'desc' => 'Display the Good Job! page after finishing reading affirmations.',
        ],
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => [
            'desc' => 'Display the affirmations habit.',
        ],
        Setting::HABITS_DAYS_TO_DISPLAY => [
            'desc' => 'Determines which days to display habit history and the toggle controls for.',
        ],
        Setting::HABITS_START_OF_WEEK => [
            'desc' => 'Determines whether the start of the week is Sunday or Monday when displaying the current week.',
        ],
    ],

    'types' => [
        Setting::TODO_MOVE_COMPLETED => 'toggle', // True or false
        Setting::TODO_SHOW_COMPLETED_FOR => 'numeric', // Number, default min/max of 0/100
        Setting::AFFIRMATIONS_SHOW_READ => 'toggle', // True or false
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => 'toggle', // True or false
        Setting::HABITS_DAYS_TO_DISPLAY => 'options', // Based on config('settings.options)
        Setting::HABITS_START_OF_WEEK => 'options', // Based on config('settings.options)
    ]
];