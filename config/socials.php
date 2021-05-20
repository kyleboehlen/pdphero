<?php
return [
    'discord' => [
        'icon_name' => 'discord',
        'icon_type' => '.png',
        'invite_id' => env('DISCORD_INVITE_ID'),
        'url' => 'https://discord.gg/{invite_id}',
    ],
    'reddit' => [
        'icon_name' => 'reddit',
        'icon_type' => '.png',
        'subreddit' => env('SUBREDDIT'),
        'url' => 'https://www.reddit.com/r/{subreddit}',
    ],
    'twitter' => [
        'icon_name' => 'twitter',
        'icon_type' => '.png',
        'username' => env('TWITTER_USERNAME'),
        'url' => 'https://twitter.com/{username}',
    ],
    'youtube' => [
        'icon_name' => 'youtube',
        'icon_type' => '.png',
        'id' => env('YOUTUBE_CHANNEL_ID'),
        'url' => 'https://www.youtube.com/channel/{id}',
    ],
];