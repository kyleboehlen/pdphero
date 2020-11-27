<?php

use App\Helpers\Constants\User\Setting;

return [
    'default' => [
        Setting::TODO_MOVE_COMPLETED => '1',
        Setting::TODO_SHOW_COMPLETED_FOR => '24',
    ],

    'seed' => [
        Setting::TODO_MOVE_COMPLETED => [
            'desc' => 'Move completed To-Do items to the bottom of the To-Do list.',
        ],
        Setting::TODO_SHOW_COMPLETED_FOR => [
            'desc' => 'Display To-Do items on the list for this many hours after they have been completed.',
        ],
    ],
];