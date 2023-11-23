<?php

use Monolog\Handler\StreamHandler;

return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'stack'        => [
            'driver'   => 'stack',
            'channels' => ['single', 'sentryLogger'],
        ],
        'heimdall'     => [
            'driver' => 'custom',
            'via'    => \TradeAppOne\Domain\Logging\Heimdall\Bifrost::class
        ],
        'customSentry' => [
            'driver'  => 'monolog',
            'handler' => Monolog\Handler\RavenHandler::class,
            'with'    => [
                'dsn' => config('sentry.dsn')
            ]
        ],
        'sentryLogger' => [
            'driver' => 'custom',
            'via'    => \TradeAppOne\Domain\Logging\SentryCustomLogger::class
        ],
        'single'       => [
            'driver' => 'single',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
        ],
        'tim_log'      => [
            'driver' => 'single',
            'path'   => storage_path('logs/tim.log'),
            'level'  => 'debug',
        ],
        'daily'        => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 7,
        ],
        'slack'        => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],
        'stderr'       => [
            'driver'  => 'monolog',
            'handler' => StreamHandler::class,
            'with'    => [
                'stream' => 'php://stderr',
            ],
        ],
        'syslog'       => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],
        'errorlog'     => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],
        'testing'      => [
            'driver' => 'custom',
            'via'    => \Psr\Log\NullLogger::class,
        ],
    ],
];
