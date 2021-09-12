<?php

use App\Helpers\Constants\Goal\TimePeriod;
use App\Helpers\Constants\Goal\Type;
use App\Helpers\Constants\Goal\Status;

return [
    'time_periods' => [
        TimePeriod::WEEKLY => [
            'name' => 'Weekly',
        ],
        TimePeriod::BI_WEEKLY => [
            'name' => 'Bi-Weekly',
        ],
        TimePeriod::MONTHLY => [
            'name' => 'Monthly',
        ],
        TimePeriod::QUARTERLY => [
            'name' => 'Quarterly',
        ],
        TimePeriod::YEARLY => [
            'name' => 'Yearly',
        ],
        TimePeriod::TOTAL => [
            'name' => 'Total',
        ],
    ],

    'ahead_buffer' => [
        'days' => 12,
        'percent' => 15,
    ],

    'lagging_buffer' => [
        'days' => 7,
        'percent' => 10,
    ],

    'manual_goal_buffer' => 100,

    'statuses' => [
        Status::TBD => [
            'class' => 'tbd',
            'name' => 'To Be Determined',
            'desc' => 'The goal was just created and has yet to be worked on!',
        ],
        Status::LAGGING => [
            'class' => 'lagging',
            'name' => 'Lagging',
            'desc' => 'You\'re currently lagging behind the due dates set for this goal.',
        ],
        Status::ON_TRACK => [
            'class' => 'ontrack',
            'name' => 'On Track',
            'desc' => 'You\'re on track to complete this goal by the due date set for this goal.',
        ],
        Status::AHEAD => [
            'class' => 'ahead',
            'name' => 'Ahead of Schedule',
            'desc' => 'You\'re currently finishing this goal before the due dates, good job!',
        ],
        Status::COMPLETED => [
            'class' => 'completed',
            'name' => 'Completed',
            'desc' => 'This goal has been completed and is ready to be marked as acheived.',
        ],
    ],

    'types' => [
        Type::PARENT_GOAL => [
            'name' => 'Parent Goal',
            'desc' => 'A parent goal allows you to add several different types of sub-goals and will calculate the total progress of all of it\'s sub-goals.',
        ],
        Type::ACTION_AD_HOC => [
            'name' => 'Ad Hoc Action Plan',
            'desc' => 'An ad hoc action plan goal allows you to specify how many action plan items you need to complete in each time period you specify. You can schedule action items from a list of action items ad hoc.',
        ],
        Type::ACTION_DETAILED => [
            'name' => 'Detailed Action Plan',
            'desc' => 'A detailed action plan goal allows you to build a very specific action plan with due dates for each item.',
        ],
        Type::HABIT_BASED => [
            'name' => 'Habit Based Goal',
            'desc' => 'A habit based goal allows you to create a goal based off hitting a specified strength on a habit.',
        ],
        Type::FUTURE_GOAL => [
            'name' => 'Future Goal',
            'desc' => 'A future goal is for specifying a goal you\'d like to accomplish but aren\'t ready to plan and work on quite yet.',
        ],
        Type::MANUAL_GOAL => [
            'name' => 'Manual Goal',
            'desc' => 'A manual goal allows you to specify due dates for hitting certain progress amounts on the goal and update the progress manually.',
        ],
        Type::BUCKETLIST => [
            'name' => 'Bucketlist Goal',
            'desc' => 'A bucketlist goal allows you to create an ad hoc action plan around completing items in your bucketlist.',
        ],
    ],
];