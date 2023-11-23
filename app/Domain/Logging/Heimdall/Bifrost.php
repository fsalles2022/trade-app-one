<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

use Elastica\Client;
use Monolog\Formatter\ElasticaFormatter;
use Monolog\Handler\ElasticSearchHandler;
use Monolog\Logger;

class Bifrost
{
    const AWS_TRANSPORT    = 'aws';
    const GUZZLE_TRANSPORT = 'guzzle';
    const BIFROST_CHANNEL  = 'heimdall';

    public function __invoke(array $config)
    {
        $config  = $this->getConfiguration();
        $client  = new Client($config);
        $handler = new ElasticSearchHandler($client);

        $index     = config('heimdall.index');
        $formatter = new ElasticaFormatter($index, '_doc');
        $handler->setFormatter($formatter);

        return new Logger(self::BIFROST_CHANNEL, [$handler]);
    }

    /**
     * @return array
     */
    protected function getConfiguration(): array
    {
        $transport = config('heimdall.transport');

        switch (strtolower($transport)) {
            case self::AWS_TRANSPORT:
                return [
                    'transport' => 'AwsAuthV4',
                    'host' => config('heimdall.host'),
                    'port' => '443',
                    'aws_access_key_id' => config('heimdall.aws_access_key_id'),
                    'aws_secret_access_key' => config('heimdall.aws_secret_access_key'),
                    'aws_region' => config('heimdall.aws_region'),
                    'ssl' => true
                ];
            case self::GUZZLE_TRANSPORT:
                return [
                    'transport' => 'guzzle',
                    'host' => config('heimdall.host'),
                    'port' => config('heimdall.port'),
                ];
        }
    }
}
