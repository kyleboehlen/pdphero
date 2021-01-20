<?php

use App\Helpers\Constants\User\Setting;

return [
    'default' => [
        Setting::TODO_MOVE_COMPLETED => '1', // True
        Setting::TODO_SHOW_COMPLETED_FOR => '24',
        Setting::AFFIRMATIONS_SHOW_READ => '1', // True
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => '0', // False
        Setting::HABITS_DAYS_TO_DISPLAY => 'rolling_seven_days',
    ],

    'options' => [
        Setting::HABITS_DAYS_TO_DISPLAY => [
            // Key => display name
            'rolling_seven_days' => 'Rolling 7 Days', 
            'current_week' => 'Current Week',
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
    ],

    'types' => [
        Setting::TODO_MOVE_COMPLETED => 'toggle', // True or false
        Setting::TODO_SHOW_COMPLETED_FOR => 'text', // Number or string
        Setting::AFFIRMATIONS_SHOW_READ => 'toggle', // True or false
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => 'toggle', // True or false
        Setting::HABITS_DAYS_TO_DISPLAY => 'options', // Based on config('settings.options)
    ]
];