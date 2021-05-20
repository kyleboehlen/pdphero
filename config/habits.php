<?php

use App\Helpers\Constants\Habits\HistoryType;
use App\Helpers\Constants\Habits\Type;

return [
    'defaults' => [
        Type::AFFIRMATIONS_HABIT => [
            'name' => 'Affirmations Habit',
            'times_daily' => 9,
            'every_x_days' => 1,
        ],
        Type::JOURNALING_HABIT => [
            'name' => 'Journaling Habit',
            'times_daily' => 1,
            'every_x_days' => 1,
        ],
    ],

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

    'strength' => [
        'min_day_change' => env('HABIT_STRENGTH_MIN_DAY_CHANGE'),
        'max_day_change' => env('HABIT_STRENGTH_MAX_DAY_CHANGE'),
        'change_rate' => env('HABIT_STRENGTH_CHANGE_RATE'),
        'buffer' => env('HABIT_STRENGTH_BUFFER'),
    ],

    'types' => [
        Type::AFFIRMATIONS_HABIT => [
            'name' => 'Affirmations Habit',
        ],
        Type::USER_GENERATED => [
            'name' => 'User Generated',
        ],
        Type::JOURNALING_HABIT => [
            'name' => 'Journaling Habit',
        ],
    ],
];