<?php

namespace TimBR\Adapters;

use ErrorException;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimOrderResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(RestResponse $response)
    {
        parent::__construct($response);
        $arrayResponse = $response->toArray();
        $content       = [];

        try {
            if (data_get($arrayResponse, 'type') === 'error') {
                $originalMessage    = $arrayResponse['message'];
                $content['message'] = $originalMessage;
                $this->pushError($content);
            } else {
                $this->adapted = $arrayResponse;
            }
        } catch (ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }

    public function isSuccess(): bool
    {
        $arrayResponse = $this->getOriginal();
        return ! data_get($arrayResponse, 'type') === 'error';
    }
}
