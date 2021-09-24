<?php

return [
    'api_url' => env('CLOUDFLARE_API_URL'),
    'auth_email' => env('CLOUDFLARE_EMAIL'),
    'api_key' => env('CLOUDFLARE_API_KEY'),
    'cache_endpoint' => 'zones/{zone_id}/purge_cache',
    'zone_id' => env('CLOUDFLARE_ZONE_ID'),
];