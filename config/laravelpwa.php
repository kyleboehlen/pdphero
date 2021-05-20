<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'PDPHero'),
        'short_name' => 'PDPHero',
        'start_url' => '/',
        'background_color' => '#26130c',
        'theme_color' => '#26130c',
        'display' => 'standalone',
        'orientation'=> 'portrait',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/pwa/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/pwa/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/pwa/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/pwa/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/pwa/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/pwa/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/pwa/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/pwa/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/pwa/splash-640x1136.png',
            '750x1334' => '/pwa/splash-750x1334.png',
            '828x1792' => '/pwa/splash-828x1792.png',
            '1125x2436' => '/pwa/splash-1125x2436.png',
            '1242x2208' => '/pwa/splash-1242x2208.png',
            '1242x2688' => '/pwa/splash-1242x2688.png',
            '1536x2048' => '/pwa/splash-1536x2048.png',
            '1668x2224' => '/pwa/splash-1668x2224.png',
            '1668x2388' => '/pwa/splash-1668x2388.png',
            '2048x2732' => '/pwa/splash-2048x2732.png',
        ],
        'shortcuts' => [
            // [
            //     'name' => 'To-Do',
            //     'description' => 'Open the To-Do list.',
            //     'url' => '/todo',
            //     'icons' => [
            //         "src" => "/assets/icons/todo-white.png",
            //         "purpose" => "any"
            //     ]
            // ],
            // [
            //     'name' => 'Habits',
            //     'description' => 'Open the Habits tracker.',
            //     'url' => '/habits',
            //     'icons' => [
            //         "src" => "/assets/icons/habits-white.png",
            //         "purpose" => "any"
            //     ]
            // ],
            // [
            //     'name' => 'Goals',
            //     'description' => 'Open the Goals tool.',
            //     'url' => '/goals',
            //     'icons' => [
            //         "src" => "/assets/icons/goals-white.png",
            //         "purpose" => "any"
            //     ]
            // ],
            // [
            //     'name' => 'Journal',
            //     'description' => 'View Journal entries and timeline.',
            //     'url' => '/journal/view/list',
            //     'icons' => [
            //         "src" => "/assets/icons/journal-white.png",
            //         "purpose" => "any"
            //     ]
            // ]
        ],
        'custom' => []
    ]
];
