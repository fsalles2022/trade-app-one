<?php

return [
    'url'        => env('POWER_BI_URL', 'https://api.powerbi.com'),
    'auth_url'   => env('MICROSOFT_AUTH_URL', 'https://login.microsoftonline.com'),
    'report_url' => env('POWER_BI_REPORT_EMBED', 'https://app.powerbi.com/reportEmbed'),
    'auth'     => [
        'client_id'   => env('POWER_BI_CLIENT_ID', ''),
        'client_secret'   => env('POWER_BI_CLIENT_SECRET', ''),
        'grant_type'     => env('POWER_BI_GRANT_TYPE', ''),
        'resource' => env('POWER_BI_RESOURCE', ''),
        'username' => env('POWER_BI_USERNAME', ''),
        'password' => env('POWER_BI_PASSWORD', '')
    ],
    'isPowerBiOffline' => env('POWER_BI_DASHBOARD_OFFLINE', 'true'),
    'powerBiOfflineImage' => env('POWER_BI_DASHBOARD_OFFLINE_IMG', 'true')
];
