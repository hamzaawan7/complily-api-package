<?php

return [
    'zoom' => [
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'auth_url' => 'https://zoom.us/oauth/authorize',
        'token_url' => 'https://zoom.us/oauth/token',
        'revoke_url' => 'https://zoom.us/oauth/revoke',
        'redirect_uri' => env('ZOOM_REDIRECT_URI'),
        'get_me_url' => 'https://api.zoom.us/v2/users/me',
        'recordings_url' => 'https://api.zoom.us/v2/users',
    ],
    'close' => [
        'client_id' => env('CLOSE_CLIENT_ID'),
        'client_secret' => env('CLOSE_CLIENT_SECRET'),
        'auth_url' => 'https://app.close.com/oauth2/authorize',
        'token_url' => 'https://app.close.com/oauth2/token',
        'revoke_url' => 'https://app.close.com/oauth2/revoke',
        'redirect_uri' => env('CLOSE_REDIRECT_URI'),
        'get_me_url' => 'https://app.close.com/api/v1/me',
        'recordings_url' => 'https://app.close.com/api/v1/users',
    ],
    // Add RingCentral, Aircall, etc.
];
