<?php

use App\Helpers\Constants\User\Setting;

return [
    'default' => [
        Setting::TODO_MOVE_COMPLETED => '1', // True
        Setting::TODO_SHOW_COMPLETED_FOR => '24',
        Setting::AFFIRMATIONS_SHOW_READ => '1', // True
        Setting::HABITS_SHOW_AFFIRMATIONS_HABIT => '0', // False
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
    ],
];