<?php

namespace TimBR\Adapters\ElDorado;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class CreditCardResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        $arrayResponse = $response->toArray();
        if (data_get($arrayResponse, 'result.resultSuccess') == true) {
            $this->adapted = $arrayResponse;
        } else {
            $content['message'] = data_get($arrayResponse, 'result.resultMessage');
            $this->pushError($content);
        }
    }
}
