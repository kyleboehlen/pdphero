<?php

use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;

return [
    'history_types' => [
        HistoryType::COMPLETED => [
            'name' => 'Completed',
        ],
        HistoryType::SKIPPED => [
            'name' => 'Skipped',
        ],
        HistoryType::MISSED => [
            'name' => 'Missed',
        ],
    ],

    'types' => [
        Type::AFFIRMATIONS_HABIT => [
            'name' => 'Affirmations Habit',
        ],
        Type::USER_GENERATED => [
            'name' => 'User Generated',
        ],
    ],
];