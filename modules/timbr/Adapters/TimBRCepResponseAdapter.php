<?php

namespace TimBR\Adapters;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimBRCepResponseAdapter extends ResponseAdapterAbstract
{
    const CEP_NOT_FOUNT_MESSAGE = 'Registro nao encontrado';
    const CEP_NOT_CODE          = '-2001';

    public function __construct(RestResponse $restResponse)
    {
        $arrayResponse = $restResponse->toArray();
        try {
            if (data_get($arrayResponse, 'message')) {
                $content['message'] = trans('timBR::messages.cep.not_found');
                $this->pushError($content);
            } else {
                $this->adapted = $arrayResponse;
            }
        } catch (\ErrorException $exception) {
            $this->adapted = $arrayResponse;
        }
    }
}
