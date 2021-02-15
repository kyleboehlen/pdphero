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
        Status::AHEAD => [
            'name' => 'Ahead of Schedule',
        ],
        Status::ON_TRACK => [
            'name' => 'On Track',
        ],
        Status::LAGGING => [
            'name' => 'Lagging Behind',
        ],
        Status::TBD => [
            'name' => 'To Be Determined',
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