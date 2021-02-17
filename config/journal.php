<?php

use App\Helpers\Constants\Journal\Mood;

return [
    'moods' => [
        Mood::DEFAULT => [
            'name' => 'Default',
        ],
        Mood::NEGATIVE => [
            'name' => 'Negative',
        ],
        Mood::NEUTRAL => [
            'name' => 'Neutral',
        ],
        Mood::POSITIVE => [
            'name' => 'Positive',
        ],
    ],
];