<?php

return [
    'transport'     => env('HEIMDALL_TRANSPORT', 'guzzle'),
    'host'      => env('HEIMDALL_HOST', 'localhost'),
    'port'      => env('HEIMDALL_PORT', 9200),
    'index'     => env('HEIMDALL_INDEX', 'heimdall'),
    'aws_access_key_id'     => env('HEIMDALL_AWS_ACCESS_KEY_ID', 'heimdall'),
    'aws_secret_access_key'     => env('HEIMDALL_AWS_SECRET_ACCESS_KEY', 'heimdall'),
    'aws_region'     => env('HEIMDALL_AWS_REGION', 'us-east-1'),
    'enable'    => (bool) env('HEIMDALL_ENABLE', 'heimdall'),
    'purge'     => [],
    'whitelist' => [],
    'blacklist' => [],
    'inbound_request' => env('HEIMDALL_INBOUND_REQUEST'),
];
