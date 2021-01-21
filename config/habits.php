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

    'statuses' => [
        HistoryType::COMPLETED => [
            'style_class' => 'completed',
        ],
        HistoryType::SKIPPED => [
            'style_class' => 'skipped',
        ],
        HistoryType::MISSED => [
            'style_class' => 'missed',
        ],
        HistoryType::PARTIAL => [
            'style_class' => 'partial',
        ],
        HistoryType::TBD => [
            'style_class' => '',
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