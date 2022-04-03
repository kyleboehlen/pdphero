<?php

use App\Helpers\Constants\Journal\Mood;

return [
    'default_categories' => [
        'Dreams', 'Gratitude', 'Monkey Mind',
    ],
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