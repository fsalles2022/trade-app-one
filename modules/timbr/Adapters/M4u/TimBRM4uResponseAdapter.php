<?php

namespace TimBR\Adapters\M4u;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimBRM4uResponseAdapter extends ResponseAdapterAbstract
{
    const M4U_INSTABILITY = 'Chave inválida';

    public function __construct(RestResponse $originalResponse)
    {
        parent::__construct($originalResponse);
        $arrayResponse = $originalResponse->toArray();
        try {
            if ($this->isSuccess()) {
                $this->adapted = $arrayResponse;
            } else {
                $code    = data_get($arrayResponse, 'responseCode', '');
                $message = data_get($arrayResponse, 'responseDescription', '');
                if ($message == self::M4U_INSTABILITY) {
                    $content['message'] = trans('timBR::messages.express.instability');
                } else {
                    $content['message'] = "{$code} $message";
                }
                $this->pushError($content);
            }
        } catch (\ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }

    public function isSuccess(): bool
    {
        $arrayResponse = $this->originalResponse->toArray();
        return str_contains(data_get($arrayResponse, 'responseDescription'), 'Sucesso');
    }
}
