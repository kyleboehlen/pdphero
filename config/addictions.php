<?php

use App\Helpers\Constants\Addiction\DateFormat;
use App\Helpers\Constants\Addiction\Method;
use App\Helpers\Constants\Addiction\Milestone;
use App\Helpers\Constants\Addiction\RelapseType;

return [
    'date_formats' => [
        DateFormat::MINUTE => [
            'name' => 'Minute(s)',
            'max' => 59,
        ],
        DateFormat::HOUR => [
            'name' => 'Hour(s)',
            'max' => 23,
        ],
        DateFormat::DAY => [
            'name' => 'Day(s)',
            'max' => 364,
        ],
        DateFormat::MONTH => [
            'name' => 'Month(s)',
            'max' => 11,
        ],
        DateFormat::YEAR => [
            'name' => 'Year(s)',
            'max' => 100,
        ],
    ],
    'methods' => [
        Method::ABSTINENCE => [
            'name' => 'Abstinence',
        ],
        Method::MODERATION => [
            'name' => 'Moderation',
        ],
    ],
    'milestones' => [
        'default' => [
            Milestone::FIVE_MINUTES => [
                'name' => 'Five Minutes',
                'amount' => '5',
                'date_format' => DateFormat::MINUTE,
            ],
            Milestone::ONE_HOUR => [
                'name' => 'One Hour',
                'amount' => 1,
                'date_format' => DateFormat::HOUR,
            ],
            Milestone::ONE_DAY => [
                'name' => 'One Day',
                'amount' => 1,
                'date_format' => DateFormat::DAY,
            ],
            Milestone::ONE_WEEK => [
                'name' => 'One Week',
                'amount' => 7,
                'date_format' => DateFormat::DAY,
            ],
            Milestone::ONE_MONTH => [
                'name' => 'One Month',
                'amount' => 1,
                'date_format' => DateFormat::MONTH,
            ],
            Milestone::THREE_MONTHS => [
                'name' => 'Three Months',
                'amount' => 3,
                'date_format' => DateFormat::MONTH,
            ],
            Milestone::SIX_MONTHS => [
                'name' => 'Six Months',
                'amount' => 6,
                'date_format' => DateFormat::MONTH,
            ],
            Milestone::ONE_YEAR => [
                'name' => 'One Year',
                'amount' => 1,
                'date_format' => DateFormat::YEAR,
            ],
            Milestone::FIVE_YEARS => [
                'name' => 'Five Years',
                'amount' => 5,
                'date_format' => DateFormat::YEAR,
            ],
            Milestone::ONE_DECADE => [
                'name' => 'One Decade',
                'amount' => 10,
                'date_format' => DateFormat::YEAR,
            ],
        ],
    ],
    'relapse' =>[
        'types' => [
            RelapseType::MODERATED_USE => [
                'name' => 'Moderated Use',
            ],
            RelapseType::FULL_RELAPSE => [
                'name' => 'Full Relapse',
            ],
        ],
    ],
];