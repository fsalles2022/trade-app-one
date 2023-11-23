<?php

namespace Gateway\tests\ServerTest\Methods;

use Carbon\Carbon;
use Gateway\API\Credential;
use Gateway\API\Environment;
use Gateway\API\Gateway;
use Gateway\tests\ServerTest\GatewayMethodInterface;

class Cancel implements GatewayMethodInterface
{

    public function execute(): Gateway
    {
        $credential         = new Credential("1", "1", Environment::SANDBOX);
        $gateway            = new Gateway($credential);
        $refelectionGateway = new \ReflectionProperty(get_class($gateway), 'response');
        $refelectionGateway->setAccessible(true);
        $response = [
            'transactionId' => '25656057-A5D1-73A1-0CE9-3B75C61974B4',
            'operationId' => 5,
            'status' => 6,
            'message' => 'CANCELLED',
            'log' => '',
            'errorCode' => '',
            'order' => [
                'reference' => '',
                'currency' => 'BRL',
                'totalAmount' => 10000,
                'dateTime' => Carbon::now()->toIso8601String(),
            ],
            'processor' => [
                'acquirer' => 'CIELO',
                'acquirerId' => '26',
                'tid' => '0516011242625',
                'paymentId' => '53c87a56-71e3-47b4-aacb-a1b94c720d9e',
                'type' => 'CreditCard',
                'amount' => 10000,
                'brand' => 'Elo',
                'numberOfPayments' => 1,
                'interest' => 0,
                'currency' => 'BRL',
                'authorizationCode' => '242064',
                'urlAuthentication' => null,
                'serviceTaxAmount' => 0,
                'returnCode' => '6',
                'returnMessage' => 'Operation Successful',
                'proofOfSale' => '20190516011242625',
                'provider' => 'Simulado',
                'status' => 10,
                'receivedDate' => Carbon::now()->toDateTimeString(),
                'softDescriptor' => '',
                'capture' => true,
                'tokenCard' => '8cac7f5a00f51f1eac7ed76423233607e061061e6f7013be82f347690d22a0ca'
            ]
        ];
        $refelectionGateway->setValue($gateway, $response);
        return $gateway;
    }
}
