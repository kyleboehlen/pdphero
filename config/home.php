<?php

use App\Helpers\Constants\Home\Home;

return [
    HOME::GOALS => [
        'name' => 'Goals',
        'desc' => 'The goals tool.',
        'img' => 'icons/goals-white.png',
        'route' => 'goals',
    ],
    HOME::HABITS => [
        'name' => 'Habits',
        'desc' => 'The habits tool.',
        'img' => 'icons/habits-white.png',
        'route' => 'habits',
    ],
    HOME::JOURNAL => [
        'name' => 'Journal',
        'desc' => 'The journal tool and timeline.',
        'img' => 'icons/journal-white.png',
        'route' => 'journal',
    ],
    HOME::PROFILE => [
        'name' => 'Profile',
        'desc' => 'View user stats, manage values, nutshell, and personal rules.',
        'img' => 'icons/profile-white.png',
        'route' => 'profile',
    ],
    HOME::SETTINGS => [
        'name' => 'Settings',
        'desc' => 'Manage application settings and customize your experience.',
        'img' => 'icons/settings-white.png',
        'route' => 'profile.edit.settings',
    ],
    HOME::TODO => [
        'name' => 'To-Do',
        'desc' => 'Manage your To-Do items.',
        'img' => 'icons/todo-white.png',
        'route' => 'todo.list',
    ],
    HOME::AFFIRMATIONS => [
        'name' => 'Affirmations',
        'desc' => 'Read your affirmations.',
        'img' => 'icons/smile-white.png',
        'route' => 'affirmations',
    ],
    HOME::EMAIL_SUPPORT => [
        'name' => 'Email Support',
        'desc' => 'Send a message to our email support.',
        'img' => 'icons/email-support-white.png',
        'route' => 'support.email.form',
    ],
];