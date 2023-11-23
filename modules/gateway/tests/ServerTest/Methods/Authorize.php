<?php

namespace Gateway\Tests\ServerTest\Methods;

use Gateway\API\Credential;
use Gateway\API\Environment;
use Gateway\API\Gateway;
use Gateway\tests\ServerTest\GatewayMethodInterface;

class Authorize implements GatewayMethodInterface
{
    public function execute(): Gateway
    {
        $credential         = new Credential("1", "1", Environment::SANDBOX);
        $gateway            = new Gateway($credential);
        $refelectionGateway = new \ReflectionProperty(get_class($gateway), 'response');
        $refelectionGateway->setAccessible(true);

        $response = json_decode(file_get_contents(__DIR__ . '/../Responses/pre_authorization_authorized.json'), true);

        $refelectionGateway->setValue($gateway, $response);
        return $gateway;
    }
}
