<?php

namespace Gateway\Connection;

use Gateway\API\Gateway;
use Gateway\Exceptions\GatewayExceptions;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;

class GatewayClient
{
    public const GATEWAY = 'GATEWAY';
    private $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function execute(string $method, $transaction, $extra = null)
    {
        $start = microtime(true);
        try {
            $response = $this->getGateway($method, $transaction, $extra);
            heimdallLog()->realm(self::GATEWAY)
                ->start($start)
                ->end(microtime(true))
                ->request(ObjectHelper::convertToArray($transaction))
                ->response($response)
                ->method($method)
                ->httpClient($this->gateway)
                ->fire();
            return $response;
        } catch (\Exception $exception) {
            heimdallLog()->realm(self::GATEWAY)
                ->start($start)
                ->end(microtime(true))
                ->method($method)
                ->request(ObjectHelper::convertToArray($transaction))
                ->catchException($exception)
                ->httpClient($this->gateway)
                ->fire();
            throw GatewayExceptions::gatewayUnavailable(json_encode($exception->getMessage()));
        }
    }

    private function getGateway($method, $transaction, $extra)
    {
        return $this->gateway->$method($transaction, $extra);
    }
}
