<?php

use App\Helpers\Constants\ToDo\Priority;
use App\Helpers\Constants\ToDo\Type;

return [
    'priorities' => [
        Priority::DEFAULT => [
            'name' => 'Default',
        ],
        Priority::LOW => [
            'name' => 'Low',
        ],
        Priority::MEDIUM => [
            'name' => 'Medium',
        ],
        Priority::HIGH => [
            'name' => 'High',
        ],
    ],

    'types' => [
        Type::TODO_ITEM => [
            'name' => 'To Do Item',
        ],
        Type::RECURRING_HABIT_ITEM => [
            'name' => 'Recurring Habit Item',
        ],
        Type::SINGULAR_HABIT_ITEM => [
            'name' => 'Singular Habit Item',
        ],
        Type::ACTION_ITEM => [
            'name' => 'Action Item',
        ],
        Type::JOURNAL_HABIT_ITEM => [
            'name' => 'Journal Habit Item',
        ],
        Type::AFFIRMATIONS_HABIT_ITEM => [
            'name' => 'Affirmations Habit Item',
        ],
    ],
];