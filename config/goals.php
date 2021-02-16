<?php

use App\Helpers\Constants\Goal\AdHocPeriod;
use App\Helpers\Constants\Goal\Type;
use App\Helpers\Constants\Goal\Status;

return [
    'ad_hoc_periods' => [
        AdHocPeriod::WEEKLY => [
            'name' => 'Weekly',
        ],
        AdHocPeriod::BI_WEEKLY => [
            'name' => 'Bi-Weekly',
        ],
        AdHocPeriod::MONTHLY => [
            'name' => 'Monthly',
        ],
        AdHocPeriod::QUARTERLY => [
            'name' => 'Quarterly',
        ],
        AdHocPeriod::YEARLY => [
            'name' => 'Yearly',
        ],
    ],

    'statuses' => [
        Status::TBD => [
            'name' => 'To Be Determined',
            'desc' => 'The goal was just created and has yet to be worked on!',
        ],
        Status::LAGGING => [
            'name' => 'Lagging',
            'desc' => 'You\'re currently lagging behind the due dates set for this goal.',
        ],
        Status::ON_TRACK => [
            'name' => 'On Track',
            'desc' => 'You\'re on track to complete this goal by the due date set for this goal.',
        ],
        Status::AHEAD => [
            'name' => 'Ahead of Schedule',
            'desc' => 'You\'re currently finishing this goal before the due dates, good job!',
        ],
        Status::COMPLETED => [
            'name' => 'Completed',
            'desc' => 'This goal has been completed and is ready to be marked as acheived.',
        ],
    ],

    'types' => [
        Type::PARENT_GOAL => [
            'name' => 'Parent Goal',
        ],
        Type::ACTION_AD_HOC => [
            'name' => 'Ad Hoc Action Plan',
        ],
        Type::ACTION_DETAILED => [
            'name' => 'Detailed Action Plan',
        ],
        Type::HABIT_BASED => [
            'name' => 'Habit Based Goal',
        ],
        Type::FUTURE_GOAL => [
            'name' => 'Future Goal',
        ],
    ],
];