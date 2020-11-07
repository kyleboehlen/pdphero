<?php

use App\Helpers\Constants\ToDo\Priority;
use App\Helpers\Constants\ToDo\Type;

return [
    'priorities' => [
        Priority::DEFAULT => [
            'name' => 'Default',
            'style_class' => 'default-pri',
        ],
        Priority::LOW => [
            'name' => 'Low',
            'style_class' => 'low-pri',
        ],
        Priority::MEDIUM => [
            'name' => 'Medium',
            'style_class' => 'medium-pri',
        ],
        Priority::HIGH => [
            'name' => 'High',
            'style_class' => 'high-pri',
        ],
    ],

    'types' => [
        Type::TODO_ITEM => [
            'name' => 'To Do Item',
        ],
        Type::HABIT_ITEM => [
            'name' => 'Habit Item',
        ],
        Type::ACTION_ITEM => [
            'name' => 'Action Item',
        ],
    ],
];