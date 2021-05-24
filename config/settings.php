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
        Setting::PROFILE_SHOW_RULES => '0', // False
        Setting::HABITS_SHOW_JOURNALING_HABIT => '0', // False
        Setting::SHOW_EMPTY_TODO_ITEM => Setting::TOP_OF_LIST, // Top of list
        Setting::SHOW_EMPTY_ACTION_ITEM => Setting::DO_NOT_SHOW, // Don't show
        Setting::SHOW_EMPTY_AD_HOC_ITEM => Setting::BOTTOM_OF_LIST, // Bottom of list
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
        Setting::SHOW_EMPTY_TODO_ITEM => [
            // Key => display name
            Setting::DO_NOT_SHOW => 'Don\'t Show',
            Setting::TOP_OF_LIST => 'Top',
            Setting::BOTTOM_OF_LIST => 'Bottom',
        ],
        Setting::SHOW_EMPTY_ACTION_ITEM => [
            // Key => display name
            Setting::DO_NOT_SHOW => 'Don\'t Show',
            Setting::TOP_OF_LIST => 'Top',
            Setting::BOTTOM_OF_LIST => 'Bottom',
        ],
        Setting::SHOW_EMPTY_AD_HOC_ITEM => [
            // Key => display name
            Setting::DO_NOT_SHOW => 'Don\'t Show',
            Setting::TOP_OF_LIST => 'Top',
            Setting::BOTTOM_OF_LIST => 'Bottom',
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
        Setting::PROFILE_SHOW_RULES => [
            'desc' => 'Determines whether or not the personal rules section displays on the profile page.',
        ],
        Setting::HABITS_SHOW_JOURNALING_HABIT => [
            'desc' => 'Display the journaling habit.',
        ],
        Setting::SHOW_EMPTY_TODO_ITEM => [
            'desc' => 'How to show the add todo entry on the todo list.',
        ],
        Setting::SHOW_EMPTY_ACTION_ITEM => [
            'desc' => 'How to show the add action plan item on a goals action plan.',
        ],
        Setting::SHOW_EMPTY_AD_HOC_ITEM => [
            'desc' => 'How to show the add ad hoc item on a gols ad hoc list.',
        ],
    ],

    'types' => [
        Setting::TODO_MOVE_COMPLETED => 'toggle', // True or false
        Setting::TODO_SHOW_COMPLETED_FOR => 'numeric', // Number, default min/max of 0/100
        Setting::AFFIRMATIONS_SHOW_READ => 'toggle', // True or false
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => 'toggle', // True or false
        Setting::HABITS_DAYS_TO_DISPLAY => 'options', // Based on config('settings.options)
        Setting::HABITS_START_OF_WEEK => 'options', // Based on config('settings.options)
        Setting::PROFILE_SHOW_RULES => 'toggle', // True or false
        Setting::HABITS_SHOW_JOURNALING_HABIT => 'toggle', // True or false
        Setting::SHOW_EMPTY_TODO_ITEM => 'options', // Based on config('settings.options)
        Setting::SHOW_EMPTY_ACTION_ITEM => 'options', // Based on config('settings.options)
        Setting::SHOW_EMPTY_AD_HOC_ITEM => 'options', // Based on config('settings.options)
    ]
];