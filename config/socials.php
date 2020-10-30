<?php
return [
    'discord' => [
        'icon' => 'discord.png',
        'invite_id' => env('DISCORD_INVITE_ID'),
        'url' => 'https://discord.gg/{invite_id}',
    ],
    'reddit' => [
        'icon' => 'reddit.png',
        'subreddit' => env('SUBREDDIT'),
        'url' => 'https://www.reddit.com/r/{subreddit}',
    ],
    'twitter' => [
        'icon' => 'twitter.png',
        'username' => env('TWITTER_USERNAME'),
        'url' => 'https://twitter.com/{username}',
    ],
    'youtube' => [
        'icon' => 'youtube.png',
        'id' => env('YOUTUBE_CHANNEL_ID'),
        'url' => 'https://www.youtube.com/channel/{id}',
    ],
];