<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fathom API-Token & Site id
    |--------------------------------------------------------------------------
    |
    | You can acquire your API-Token from the url below:
    | https://app.usefathom.com/api
    |
    */
    'api_token' => env('FATHOM_API_TOKEN'),
    'site_id' => env('FATHOM_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Fathom Domain
    |--------------------------------------------------------------------------
    |
    | If you're from the EU, I can recommend using the EU CDN:
    | cdn-eu.usefathom.com
    |
    */
    'domain' => env('FATHOM_DOMAIN', 'cdn.usefathom.com'),

    /*
    |--------------------------------------------------------------------------
    | Stats cache ttl
    |--------------------------------------------------------------------------
    |
    | This value is the ttl for the displayed dashboard
    | stats values. You can increase or decrease
    | this value.
    |
    */
    'cache_time' => 300,
];
