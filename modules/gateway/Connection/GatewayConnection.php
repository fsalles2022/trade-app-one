<?php

namespace Gateway\Connection;

use Gateway\API\Gateway;
use Gateway\API\Tokenization;
use Gateway\Components\Transaction;
use Gateway\Helpers\GatewayMethodsEnum;

class GatewayConnection
{
    protected $client;

    public function __construct(GatewayClient $client)
    {
        $this->client = $client;
    }

    public function authorize(Transaction $transaction): Gateway
    {
        return $this->client->execute(GatewayMethodsEnum::AUTHORIZE, $transaction);
    }

    public function sale(Transaction $transaction): Gateway
    {
        return $this->client->execute(GatewayMethodsEnum::SALE, $transaction);
    }

    public function cancel(string $transactionId, $extra = null): Gateway
    {
        return $this->client->execute(GatewayMethodsEnum::CANCEL, $transactionId, $extra);
    }

    public function tokenize(Transaction $transaction): Tokenization
    {
        return $this->client->execute(GatewayMethodsEnum::TOKENIZE, $transaction);
    }
}
