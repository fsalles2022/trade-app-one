<?php

namespace VivoBR\Adapters\Response;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class UserResponseAdapter extends ResponseAdapterAbstract
{
    const SUCCESS_FLAG = '0';

    public function __construct(RestResponse $restResponse)
    {
        parent::__construct($restResponse);
        $this->originalResponse = $restResponse;
        $details                = data_get($this->originalResponse->toArray(), 'detalhes');
        $content['message']     = data_get($this->originalResponse->toArray(), 'detalhes.0', json_encode($details));
        $this->adapted          = $content;
    }

    public function isSuccess(): bool
    {
        $arrayResponse = $this->originalResponse->toArray();
        return data_get($arrayResponse, 'codigo') == self::SUCCESS_FLAG;
    }
}
