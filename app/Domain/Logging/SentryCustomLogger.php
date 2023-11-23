<?php

namespace TradeAppOne\Domain\Logging;

use Monolog\Logger;
use Raven_Client;

class SentryCustomLogger
{
    public function __invoke(array $config)
    {
        $client = new Raven_Client(config('sentry.dsn'));

        $handler = new \Monolog\Handler\RavenHandler($client);
        $handler->setFormatter(new \Monolog\Formatter\LineFormatter("%message% %context% %extra%\n"));
        return new Logger('sentry', [$handler]);
    }
}
